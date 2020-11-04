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
                font-size: 1em;
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
            try {
                include_once('appConnection.php');
                
                //buscando os dados do orçamento/pedido

                $py = md5('idpedido');
                $sql = $pdo->prepare("SELECT pedido.idpedido,pedido.codigo,pedido.datado,pedido.hora,pedido.desconto,pedido.forma_pagamento,vendedor.nome AS vendedor,cliente.nome AS cliente,cliente.cpf_cnpj,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular,cliente.email FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente WHERE pedido.idpedido = :idpedido");
                $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $codigo = $lin->codigo;
                        $desconto = $lin->desconto;
                        
                            switch($lin->forma_pagamento) {
                                case 'avista': $forma_pagamento = '&Agrave; vista'; break;
                                case '7dias': $forma_pagamento = '7 dias'; break;
                                case '14dias': $forma_pagamento = '14 dias'; break;
                                case '21dias': $forma_pagamento = '21 dias'; break;
                                case '28dias': $forma_pagamento = '28 dias'; break;
                            }

                        //invertendo a data
                        $ano = substr($lin->datado,0,4);
                        $mes = substr($lin->datado,5,2);
                        $dia = substr($lin->datado,8);
                        $lin->datado = $dia."/".$mes."/".$ano;

                            //verificando CPF ou CNPJ
                            if(strlen($lin->cpf_cnpj) == 14){
                                $doc = '<div class="col-md-3 invoice-col">CPF: '.$lin->cpf_cnpj.'</div>';
                            } else if(strlen($lin->cpf_cnpj) == 18){
                                $doc = '<div class="col-md-3 invoice-col">CNPJ: '.$lin->cpf_cnpj.'</div>';
                            }

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

                            <div class="row invoice-info">
                                <div class="col-md-3 invoice-col"><strong>'.$lin->codigo.'</strong></div>
                                <div class="col-md-3 invoice-col"><strong>'.$lin->vendedor.'</strong></div>
                                <div class="col-md-6 invoice-col">'.$lin->datado.' &#45; '.$lin->hora.' h</div>
                            </div>
                            
                            <div class="row invoice-info">
                                <div class="col-md-6 invoice-col">'.$lin->cliente.'</div>
                                '.$doc.'
                                <div class="col-md-3 invoice-col">'.$lin->email.'</div>
                            </div>
                            <div class="row invoice-info">
                                <div class="col-md-9 invoice-col"><address>'.$lin->endereco.' &#45; '.$lin->bairro.' &#45; '.$lin->cidade.' &#45; '.$lin->estado.'</address></div>
                                <div class="col-md-3 invoice-col">'.$lin->telefone.' &#45; '.$lin->celular.'</div>
                            </div>';

                        unset($ano,$mes,$dia,$lin,$doc);
                    }
                
                unset($sql,$ret);

                //buscando os produtos do orçamento/pedido

                $sql = $pdo->prepare("SELECT produto.descricao,produto.valor_venda,produto_no_pedido.quantidade,produto_no_pedido.subtotal,produto_no_pedido.bonus FROM produto_no_pedido INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN produto ON produto_no_pedido.produto_idproduto = produto.idproduto WHERE pedido.idpedido = :idpedido");
                $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py2 = md5('idproduto');
                        $total = 0;

                        echo'
                            <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="print-th-1">Descri&ccedil;&atilde;o</th>
                                                <th class="print-th-2">Valor de venda</th>
                                                <th class="print-th-3">Quantidade</th>
                                                <th class="print-th-4" style="width: 300px;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                        
                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                if($lin->bonus > 0) {
                                    $lin->bonus = '<span class="pull-right label label-warning">'.$lin->bonus.' de b&ocirc;nus</span>';
                                }

                                echo'
                                <tr>
                                    <td><span>'.$lin->descricao.'</span>'.$lin->bonus.'</td>
                                    <td>R$ '.number_format($lin->valor_venda,2,'.',',').'</td>
                                    <td>'.$lin->quantidade.'</td>
                                    <td>R$ '.number_format($lin->subtotal,2,'.',',').'</td>
                                </tr>';

                                $total = $total + $lin->subtotal;
                            }
                        
                        //Calculando o desconto, se houver
                        if(!empty($desconto)) {
                            $a = $total * $desconto;
                            $a = number_format($a,2,'.','');
                            $b = $a / 100;
                            $desconto2 = $total - $b;
                        } else {
                            $desconto = 0;
                            $desconto2 = $total;
                        }

                        echo'
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"></td>
                                                <th>Total: R$ '.number_format($total,2,'.',',').'</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Forma de pagamento: '.$forma_pagamento.' <br> Desconto: '.$desconto.' %</td>
                                                <th>Total com desconto: R$ '.number_format($desconto2,2,'.',',').'</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!--<div class="row hidden-sm hidden-md hidden-lg">
                                    <div class="col-md-12">';
                                        if($_SESSION['key'] == 'A') {
                                            echo'<a class="btn btn-default btn-flat" href="inicio-adm">Pular</a>';
                                        } elseif($_SESSION['key'] == 'R') {
                                            echo'<a class="btn btn-default btn-flat" href="inicio-adm">Pular</a>';
                                        } elseif($_SESSION['key'] == 'U') {
                                            echo'<a class="btn btn-default btn-flat" href="inicio">Pular</a>';
                                        }
                                    echo'
                                    </div>
                                </div>-->
                            </div>

                        </section>';
                        
                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' imprimiu o orçamento/pedido '.$codigo.'.';

                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                        $res_log = $sql_log->execute();

                            if(!$res_log) {
                                var_dump($sql_log->errorInfo());
                            }

                        unset($py2,$lin,$total,$codigo,$desconto,$desconto2,$forma_pagamento,$a,$b,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                    }

                unset($py,$sql,$ret);
            }
            catch(PDOException $e) {
                echo'Erro ao conectar o servidor '.$e->getMessage();
            }
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
<?php unset($pdo,$e,$cfg); ?>