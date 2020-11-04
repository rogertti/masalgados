<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* CONTROLE DE VARIAVEL */

    if(empty($_POST['rand'])) { die('null'); }
    if(empty($_POST['vendedor'])) { die('null'); } else {
        $filtro = 1;
    
        $vendedor = explode('-', $_POST['vendedor']);
    }
    if(empty($_POST['periodo'])) { die('null'); } else {
        $filtro++;
        
        $periodo = explode(' - ', $_POST['periodo']);

        $dia = substr($periodo[0],0,2);
        $mes = substr($periodo[0],3,2);
        $ano = substr($periodo[0],6);
        $data_inicial = $ano."-".$mes."-".$dia;

        $dia = substr($periodo[1],0,2);
        $mes = substr($periodo[1],3,2);
        $ano = substr($periodo[1],6);
        $data_final = $ano."-".$mes."-".$dia;

        unset($dia,$mes,$ano);
    }

    if($filtro == 2) {
        try {
            include_once('appConnection.php');
            
            //Buscando o idpedido,vendedor,descrição,quantidade,valor_venda e subtototal

            $monitor = 'T';
            #$sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.quantidade * produto.valor_venda) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
            $sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.subtotal) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
            $sql->bindParam(':data_inicial', $data_inicial, PDO::PARAM_STR);
            $sql->bindParam(':data_final', $data_final, PDO::PARAM_STR);
            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
            $sql->bindParam(':idvendedor', $vendedor[0], PDO::PARAM_INT);
            $sql->execute();
            $ret = $sql->rowCount();

                if($ret > 0) {
                    $py_data_inicial = md5('data_inicial');
                    $py_data_final = md5('data_final');
                    $py_idvendedor = md5('idvendedor');
                    $py_vendedor = md5('vendedor');
                    $total = 0;
                    $total_bruto = 0;
                    $total_liquido = 0;

                    //Obtendo o total bruto e o total líquido dos pedidos

                    $sql2 = $pdo->prepare("SELECT pedido.codigo,pedido.desconto,SUM(produto_no_pedido.subtotal) AS subtotal_bruto FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN produto_no_pedido ON pedido.idpedido = produto_no_pedido.pedido_idpedido INNER JOIN produto ON produto.idproduto = produto_no_pedido.produto_idproduto WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY pedido.codigo");
                    $sql2->bindParam(':data_inicial', $data_inicial, PDO::PARAM_STR);
                    $sql2->bindParam(':data_final', $data_final, PDO::PARAM_STR);
                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                    $sql2->bindParam(':idvendedor', $vendedor[0], PDO::PARAM_INT);
                    $sql2->execute();
                    $ret2 = $sql2->rowCount();

                        if($ret2 > 0) {
                            if($ret2 > 1) {
                                $total_pedido = $ret2.' or&ccedil;amentos/pedidos';
                            } else {
                                $total_pedido = $ret2.' or&ccedil;amento/pedido';
                            }                            

                            while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                if($lin2->desconto > 0) {
                                    $a = $lin2->subtotal_bruto * $lin2->desconto;
                                    $b = $a / 100;
                                    $subtotal_liquido = $lin2->subtotal_bruto - $b;
                                } else {
                                    $subtotal_liquido = $lin2->subtotal_bruto;
                                }

                                $total_bruto = $total_bruto + $lin2->subtotal_bruto;
                                $total_liquido = $total_liquido + $subtotal_liquido;

                                unset($a,$b);
                            }
                        }

                    unset($sql2,$ret2,$lin2);

                    echo'
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-nodata dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th colspan="4">Vendedor: '.$vendedor[1].' &#124; Entre '.$periodo[0].' e '.$periodo[1].'</th>
                                </tr>
                                <tr>
                                    <th colspan="4">'.$total_pedido.' &#124; Total Bruto: R$ '.number_format($total_bruto,2,'.',',').' &#124; Total L&iacute;quido: R$ '.number_format($total_liquido,2,'.',',').'</th>
                                </tr>
                                <tr>
                                    <th>Descri&ccedil;&atilde;o</th>
                                    <th>Valor de venda</th>
                                    <th>Quantidade</th>
                                    <th style="width: 300px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>';

                    while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                        echo'
                                <tr>
                                    <td>'.$lin->descricao.'</td>
                                    <td>R$ '.number_format($lin->valor_venda,2,'.',',').'</td>
                                    <td>'.$lin->quantidade.'</td>
                                    <td>R$ '.number_format($lin->subtotal,2,'.',',').'</td>
                                </tr>';

                        $total = $total + $lin->subtotal;
                    }

                    echo'
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <th>Total: R$ '.number_format($total,2,'.',',').'</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-primary btn-flat pull-right" href="romaneioPrint.php?'.$py_data_inicial.'='.$data_inicial.'&'.$py_data_final.'='.$data_final.'&'.$py_idvendedor.'='.$vendedor[0].'&'.$py_vendedor.'='.$vendedor[1].'">Imprimir</a>
                        </div>
                    </div>';

                    //Registrando LOG

                    $log_datado = date('Y-m-d');
                    $log_hora = date('H:i:s');
                    $log_descricao = 'Usuário '.$_SESSION['seller'].' gerou o romaneio do vendedor '.$vendedor[1].' no intervalo de '.$periodo[0].' até '.$periodo[1].'.';

                    $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                    $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                    $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                    $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                    $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                    $res_log = $sql_log->execute();

                        if(!$res_log) {
                            var_dump($sql_log->errorInfo());
                        }

                    unset($lin,$total,$total_bruto,$total_liquido,$total_pedido,$py_data_final,$py_data_inicial,$py_idvendedor,$py_vendedor,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                } else {
                    echo'<p class="lead text-center">Nenhum romaneio gerado nesses parâmentros.</p>';
                }

            unset($sql,$ret,$monitor);
        }
        catch(PDOException $e) {
            echo'Falha ao conectar o servidor '.$e->getMessage();
        }
    }//if filtro
    
    unset($pdo,$e,$filtro,$vendedor,$periodo,$data_final,$data_inicial);
?>