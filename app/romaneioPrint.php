<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
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
        <section class="wrapper">
        <?php
            try {
                include_once('appConnection.php');
                
                //Buscando o idpedido, vendedor, descrição,quantidade,valor_venda e subtototal

                $py_data_inicial = md5('data_inicial');
                $py_data_final = md5('data_final');
                $py_idvendedor = md5('idvendedor');
                $py_vendedor = md5('vendedor');
                $monitor = 'T';

                #$sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.quantidade * produto.valor_venda) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
                $sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,SUM(produto_no_pedido.quantidade) AS quantidade,SUM(produto_no_pedido.subtotal) AS subtotal FROM produto INNER JOIN produto_no_pedido ON produto_no_pedido.produto_idproduto = produto.idproduto INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY produto.descricao ORDER BY produto.descricao");
                $sql->bindParam(':data_inicial', $_GET[''.$py_data_inicial.''], PDO::PARAM_STR);
                $sql->bindParam(':data_final', $_GET[''.$py_data_final.''], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $total = 0;
                        $total_bruto = 0;
                        $total_liquido = 0;

                        //Obtendo o total bruto e o total líquido dos pedidos

                        $sql2 = $pdo->prepare("SELECT pedido.codigo,pedido.desconto,SUM(produto_no_pedido.subtotal) AS subtotal_bruto FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN produto_no_pedido ON pedido.idpedido = produto_no_pedido.pedido_idpedido INNER JOIN produto ON produto.idproduto = produto_no_pedido.produto_idproduto WHERE (pedido.datado BETWEEN :data_inicial AND :data_final) AND pedido.monitor = :monitor AND vendedor.idvendedor = :idvendedor GROUP BY pedido.codigo");
                        $sql2->bindParam(':data_inicial', $_GET[''.$py_data_inicial.''], PDO::PARAM_STR);
                        $sql2->bindParam(':data_final', $_GET[''.$py_data_final.''], PDO::PARAM_STR);
                        $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                        $sql2->bindParam(':idvendedor', $_GET[''.$py_idvendedor.''], PDO::PARAM_INT);
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
                                }

                                unset($a,$b);
                            }

                        unset($sql2,$ret2,$lin2);

                        //invertendo a data

                        $ano = substr($_GET[''.$py_data_inicial.''],0,4);
                        $mes = substr($_GET[''.$py_data_inicial.''],5,2);
                        $dia = substr($_GET[''.$py_data_inicial.''],8);
                        $data_inicial = $dia."/".$mes."/".$ano;

                        $ano = substr($_GET[''.$py_data_final.''],0,4);
                        $mes = substr($_GET[''.$py_data_final.''],5,2);
                        $dia = substr($_GET[''.$py_data_final.''],8);
                        $data_final = $dia."/".$mes."/".$ano;

                        echo'
                        <!-- Main content -->
                        <section class="invoice">

                            <!-- title row -->
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

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="4">Vendedor: '.$_GET[''.$py_vendedor.''].' &#124; Entre '.$data_inicial.' e '.$data_final.'</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4">'.$total_pedido.' &#124; Total Bruto: R$ '.number_format($total_bruto,2,'.',',').' &#124; Total L&iacute;quido: R$ '.number_format($total_liquido,2,'.',',').'</th>
                                            </tr>
                                            <tr>
                                                <th class="print-th-1">Descri&ccedil;&atilde;o</th>
                                                <th class="print-th-2">Valor de venda</th>
                                                <th class="print-th-3">Quantidade</th>
                                                <th class="print-th-4" style="width: 300px;">Subtotal</th>
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
                            </div>

                        </section>';

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' imprimiu o romaneio do vendedor '.$_GET[''.$py_vendedor.''].' no intervalo de '.$data_inicial.' até '.$data_final.'.';

                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                        $res_log = $sql_log->execute();

                            if(!$res_log) {
                                var_dump($sql_log->errorInfo());
                            }

                        unset($lin,$total,$total_bruto,$total_liquido,$total_pedido,$data_inicial,$data_final,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                    }//if ret

                unset($sql,$ret,$py_data_final,$py_data_inicial,$py_idvendedor,$py_vendedor,$monitor);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
            
            unset($pdo,$e);
        ?>
        </div>
        <!-- /.wrapper -->

        <script src="js/jquery-2.2.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(window).load(function () {
                window.onafterprint = function(e){
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

                /* Esse método é o correto, mas no Chrome não funciona.
                print();

                var beforePrint = function() {
                    console.log('Antes de imprimir...');
                };

                var afterPrint = function() {
                    console.log('Depois de imprimir...');
                    location.href = 'inicio.php';
                };

                if (window.matchMedia) {
                    var mediaQueryList = window.matchMedia('print');
                    mediaQueryList.addListener(function(mql) {
                        if (mql.matches) {
                            beforePrint();
                        } else {
                            afterPrint();
                        }
                    });
                }

                window.onbeforeprint = beforePrint;
                window.onafterprint = afterPrint;*/
            });
        </script>
    </body>
</html>