<?php
    require_once('appConfig.php');
    include_once('appConnection.php');

        if (empty($_SESSION['key'])) {
            header('location:./');
        }

    /* CLEAR CACHE */
        
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $box_option = explode(',', $_GET['box_option']);
    $py_idvendedor = md5('idvendedor');
    $py_vendedor = md5('vendedor');
    $total = 0;
    $total_bruto = 0;
    $total_liquido = 0;
    $romaneio = '';

        if (count($box_option) > 1) {
            $total_pedido = count($box_option) . ' or&ccedil;amentos/pedidos';
        } else {
            $total_pedido = '1 or&ccedil;amento/pedido';
        }
    
        foreach ($box_option as $idpedido) {
            try {
                //Buscando o idpedido,vendedor,descrição,quantidade,valor_venda e subtototal
    
                $monitor = 'T';
                $sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.subtotal) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE pedido.monitor = :monitor AND pedido.idpedido = :idpedido AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
                $sql->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
                $sql->execute();
                $ret = $sql->rowCount();

                if ($ret > 0) {
                    //Obtendo o total bruto e o total líquido dos pedidos

                    $sql2 = $pdo->prepare("SELECT pedido.codigo,pedido.desconto,SUM(produto_no_pedido.subtotal) AS subtotal_bruto FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN produto_no_pedido ON pedido.idpedido = produto_no_pedido.pedido_idpedido INNER JOIN produto ON produto.idproduto = produto_no_pedido.produto_idproduto WHERE pedido.monitor = :monitor AND pedido.idpedido = :idpedido AND vendedor.idvendedor = :idvendedor GROUP BY pedido.codigo ");
                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                    $sql2->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
                    $sql2->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
                    $sql2->execute();
                    $ret2 = $sql2->rowCount();

                    if ($ret2 > 0) {
                        while ($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                            if ($lin2->desconto > 0) {
                                $a = $lin2->subtotal_bruto * $lin2->desconto;
                                $b = $a / 100;
                                $subtotal_liquido = $lin2->subtotal_bruto - $b;
                            } else {
                                $subtotal_liquido = $lin2->subtotal_bruto;
                            }

                            $total_bruto = $total_bruto + $lin2->subtotal_bruto;
                            $total_liquido = $total_liquido + $subtotal_liquido;
                        }
                    }

                    $sql2->closeCursor();
                    unset($sql2,$ret2,$lin2,$a,$b);

                    while ($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                        $romaneio .= '
                                <tr>
                                    <td>'.$lin->descricao.'</td>
                                    <td>R$ '.number_format($lin->valor_venda, 2, '.', ',').'</td>
                                    <td>'.$lin->quantidade.'</td>
                                    <td>R$ '.number_format($lin->subtotal, 2, '.', ',').'</td>
                                </tr>';
        
                        $total = $total + $lin->subtotal;
                    }
                } else {
                    echo'<p class="lead text-center">Nenhum romaneio gerado nesses parâmentros.</p>';
                }

                $sql->closeCursor();
            } catch (PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        }
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Romaneio por pedido</h4>
</div>
<div class="modal-body overing">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-nodata dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th colspan="4">Vendedor: <?php echo $_SESSION['seller']; ?></th>
                        </tr>
                        <tr>
                            <th colspan="4"><?php echo $total_pedido; ?> &#124; Total Bruto: R$ <?php echo number_format($total_bruto, 2, '.', ','); ?> &#124; Total L&iacute;quido: R$ <?php echo number_format($total_liquido, 2, '.', ','); ?></th>
                        </tr>
                        <tr>
                            <th>Descri&ccedil;&atilde;o</th>
                            <th>Valor de venda</th>
                            <th>Quantidade</th>
                            <th style="width: 300px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php echo $romaneio; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3"></th>
                            <th>Total: R$ <?php echo number_format($total, 2, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
    <a class="btn btn-default btn-flat pull-left" data-toggle="modal" data-target="#modal-add-romaneio-note" href="#">Adicionar Nota</a>
    <button type="button" class="btn btn-primary btn-flat btn-print-romaneio">Imprimir</button>
</div>

<div class="modal fade" id="modal-add-romaneio-note" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-note" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Adicione uma nota, uma observação, etc</h4>
            </div>
            <div class="modal-body overing">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text text-danger" for="note"></label>
                            <div class="input-group col-md-12">
                                <textarea name="note" id="note" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left btn-close-note">Fechar</button>
                <button type="button" class="btn btn-primary btn-flat btn-add-romaneio-note">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".btn-add-romaneio-note").click(function(e) {
            e.preventDefault();

            let inputNote = document.getElementById("note");
            
            localStorage.setItem("romaneioNote", inputNote.value);
            
            $.smkAlert({text: "Nota de romaneio adicionada com sucesso.", type: "success", time: 3});
            
            $("#modal-add-romaneio-note").modal('hide');
        });

        $('.btn-print-romaneio').click(function(e) {
            e.preventDefault();
            
            location.href = 'romaneioPrintBox.php?box_option=<?php echo $_GET['box_option']; ?>&<?php echo $py_idvendedor; ?>=<?php echo $_GET[''.$py_idvendedor.'']; ?>';
        });

        $('.btn-close-note').click(function(e) {
            e.preventDefault();
            
            $('#modal-add-romaneio-note').modal('hide');
        });
    });
</script>