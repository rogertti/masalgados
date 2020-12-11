<?php
    require_once('appConfig.php');

        if (empty($_SESSION['key'])) {
            header('location:./');
        }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title><?php echo $cfg['title']; ?></title>
        <link rel="icon" type="image/png" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/ionicons.min.css">
        <link rel="stylesheet" href="css/core.css">
        <link rel="stylesheet" href="css/skin-black.min.css">
        <style media="print">
            * {
                overflow: visible !important;
            }
            
            body {
                font-size: 0.8em;
            }

            h2.page-header {
                font-size: 1.8em;
                vertical-align: middle;
            }

            .logo img {
                width: 100px !important;
            }

            .table>thead>tr>th {
                padding: 2px !important;;
            }

           .table>tbody>tr>td {
                padding: 2px !important;;
            }

            .table>tfoot>tr>th {
                padding: 2px !important;;
            }

            .table>tfoot>tr>td {
                padding: 2px !important;;
            }

            .print-th-1 {
                width: 1000px;
            }

            .print-th-2 {
                width: 150px;
            }

            .print-th-3 {
                width: 100px;
            }

            .print-th-4 {
                width: 300px;
            }
        </style>
        <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><script src="js/respond.min.js"></script><![endif]-->
    </head>
    <body>
        <div class="wrapper">
            <?php
                $box_option = explode(',', $_GET['box_option']);
                $py_idvendedor = md5('idvendedor');
                $py_vendedor = md5('vendedor');

                    if (count($box_option) > 1) {
                        $total_pedido = count($box_option) . ' or&ccedil;amentos/pedidos';
                    } else {
                        $total_pedido = '1 or&ccedil;amento/pedido';
                    }
                
                        try {
                            include_once('appConnection.php');

                            //Buscando o idpedido,vendedor,descrição,quantidade,valor_venda e subtototal
                
                            $monitor = 'T';
                            $sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.subtotal) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE pedido.monitor = :monitor AND pedido.idpedido IN (".$_GET['box_option'].") AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
                            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                            #$sql->bindParam(':idpedido', $_GET['box_option'], PDO::PARAM_STR);
                            $sql->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
                            $sql->execute();

                                if ($sql->rowCount() > 0) {
                                    $total = 0;

                                    //Obtendo o total bruto e o total líquido dos pedidos

                                    $sql2 = $pdo->prepare("SELECT pedido.codigo,pedido.desconto,SUM(produto_no_pedido.subtotal) AS subtotal_bruto FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN produto_no_pedido ON pedido.idpedido = produto_no_pedido.pedido_idpedido INNER JOIN produto ON produto.idproduto = produto_no_pedido.produto_idproduto WHERE pedido.monitor = :monitor AND pedido.idpedido IN (".$_GET['box_option'].") AND vendedor.idvendedor = :idvendedor GROUP BY pedido.codigo");
                                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                    #$sql2->bindParam(':idpedido', $_GET['box_option'], PDO::PARAM_STR);
                                    $sql2->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
                                    $sql2->execute();

                                        if ($sql2->rowCount() > 0) {
                                            $total_bruto = 0;
                                            $total_liquido = 0;

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

                                    echo'
                                    <section class="invoice">
                                    
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <h2 class="page-header">
                                                    <!--<i class="fa fa-globe"></i> Salgados MA-->
                                                    <span class="logo"><img src="img/logo.png" title="Salgados Marlon Ant&ocirc;nio" alt="Salgados MA" style="width: 125px;"></span>
                                                    <small class="pull-right">
                                                        47 3365&#45;2152 &#124;
                                                        47 9 8482&#45;2152 &#124;
                                                        contato@salgadosmarlonantonio.com.br
                                                    </small>
                                                </h2>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12 table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="4">Vendedor: ' . $_SESSION['seller'] . '</th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="4">' . $total_pedido . ' &#124; Total Bruto: R$ ' . number_format($total_bruto, 2, '.', ',') . ' &#124; Total L&iacute;quido: R$ ' . number_format($total_liquido, 2, '.', ',') . '</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="print-th-1">Descri&ccedil;&atilde;o</th>
                                                            <th class="print-th-2">Valor de venda</th>
                                                            <th class="print-th-3">Quantidade</th>
                                                            <th class="print-th-4" style="width: 300px;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';

                                        while ($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                            echo'
                                                        <tr>
                                                            <td>'.$lin->descricao.'</td>
                                                            <td>R$ '.number_format($lin->valor_venda, 2, '.', ',').'</td>
                                                            <td>'.$lin->quantidade.'</td>
                                                            <td>R$ '.number_format($lin->subtotal, 2, '.', ',').'</td>
                                                        </tr>';
                            
                                            $total = $total + $lin->subtotal;
                                        }

                                    echo'
                                                    </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="3"></th>
                                                                <th>Total: R$ ' . number_format($total, 2, '.', ',') . '</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <em class="p-romaneio-note"><strong></strong></em>
                                                </div>
                                            </div>
                                        </section>';
                                } else {
                                    echo'<p class="lead text-center">Nenhum romaneio gerado nesses parâmetros.</p>';
                                }
                        } catch (PDOException $e) {
                            echo'Falha ao conectar o servidor '.$e->getMessage();
                        }
?>
        </div>
        <!-- /.wrapper -->

        <script src="js/jquery-2.2.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(window).load(function () {
                $('.p-romaneio-note strong').html('Observa&ccedil;&atilde;o:' + localStorage.getItem('romaneioNote'));
                
                window.onafterprint = function(e) {
                    $(window).off('mousemove', window.onafterprint);
                    <?php if($_SESSION['key'] == 'A') { ?>
                    location.href = 'inicio-adm';
                    <?php } elseif($_SESSION['key'] == 'R') { ?>
                    location.href = 'inicio-adm';
                    <?php } elseif($_SESSION['key'] == 'U') { ?>
                    location.href = 'inicio';
                    <?php } ?>
                };

                window.print();

                setTimeout(function(){
                    $(window).on('mousemove', window.onafterprint);
                }, 1);
            });
        </script>
    </body>
</html>