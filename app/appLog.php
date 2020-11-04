<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 0;

    $getmes = md5('mes');
    $getano = md5('ano');

        if(isset($_GET[''.$getmes.''])) {
            $mes = $_GET[''.$getmes.''];
        } else {
            $mes = date('m');
        }

        if(isset($_GET['left'])) {
            if($mes == '12') {
                $ano = $_GET[''.$getano.''] - 1;
            } else {
                $ano = $_GET[''.$getano.''];
            }
        }

        if(isset($_GET['right'])) {
            if($mes == '01') {
                $ano = $_GET[''.$getano.''] + 1;
            } else {
                $ano = $_GET[''.$getano.''];
            }
        }

        if(isset($_GET['pick'])) {
            $ano = $_GET[''.$getano.''];
        }

        if ((!isset($_GET['left'])) and (!isset($_GET['right'])) and (!isset($_GET['pick']))) {
            $ano = date('Y');
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
        <link rel="stylesheet" href="css/smoke.min.css">
        <link rel="stylesheet" href="css/datepicker.min.css">
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="css/dataTables.responsive.bootstrap.min.css">
        <link rel="stylesheet" href="css/core.css">
        <link rel="stylesheet" href="css/skin-black.min.css">
        <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><script src="js/respond.min.js"></script><![endif]-->
    </head>
    <body class="hold-transition skin-black sidebar-mini sidebar-collapse">
        <!-- Site wrapper -->
        <div class="wrapper">
            <?php
                include_once('appHeader.php');
                include_once('appSidebar.php');
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <span>Logs</span>
                        <span class="pull-right lead"><a class="btn btn-danger btn-flat btn-clear-log" title="Limpar os Logs" href="#"><i class="fa fa-trash"></i> Limpar</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            function mes_extenso ($fmes) {
                                switch ($fmes) {
                                    case '01': $fmes = 'Janeiro'; break;
                                    case '02': $fmes = 'Fevereiro'; break;
                                    case '03': $fmes = 'Mar&ccedil;o'; break;
                                    case '04': $fmes = 'Abril'; break;
                                    case '05': $fmes = 'Maio'; break;
                                    case '06': $fmes = 'Junho'; break;
                                    case '07': $fmes = 'Julho'; break;
                                    case '08': $fmes = 'Agosto'; break;
                                    case '09': $fmes = 'Setembro'; break;
                                    case '10': $fmes = 'Outubro'; break;
                                    case '11': $fmes = 'Novembro'; break;
                                    case '12': $fmes = 'Dezembro'; break;
                                }

                                return $fmes;
                            }
                            
                            $mesleft = $mes - 1;
                            $mesright = $mes + 1;

                                if(strlen($mesleft) == 1) {
                                    $mesleft = '0'.$mesleft;

                                        if($mesleft == '00') {
                                            $mesleft = '12';
                                        }
                                }

                                if(strlen($mesright) == 1) {
                                    $mesright = '0'.$mesright;

                                        if($mesright == '13') {
                                            $mesright = '01';
                                        }
                                } else {
                                    if($mesright == '13') {
                                        $mesright = '01';
                                    }
                                }

                            echo'
                            <div class="div-time">
                                <div class="div-time-left text-center">
                                    <a class="lead" href="log?'.$getmes.'='.$mesleft.'&'.$getano.'='.$ano.'&left=1" title="M&ecirc;s anterior">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>
                                </div>
                                <div class="div-time-center">
                                    <p class="lead text-center">
                                        <!--<span class="text-bold text-uppercase">'.mes_extenso($mes).' de '.$ano.'</span>-->
                                        <input type="text" class="date-pick text-center" value="'.mes_extenso($mes).' de '.$ano.'" readonly>
                                    </p>
                                </div>
                                <div class="div-time-right text-center">
                                    <a class="lead" href="log?'.$getmes.'='.$mesright.'&'.$getano.'='.$ano.'&right=1" title="Pr&oacute;ximo m&ecirc;s">
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <hr>';

                            try {
                                include_once('appConnection.php');
                                
                                //buscando os orçamentos/pedidos
                                $datado = $ano.'-'.$mes.'%';
                                $sql = $pdo->prepare("SELECT vendedor.nome AS vendedor,log.datado,log.hora,log.descricao FROM log INNER JOIN vendedor ON log.vendedor_idvendedor = vendedor.idvendedor WHERE log.datado LIKE :datado ORDER BY log.datado DESC,log.hora DESC,log.descricao");
                                $sql->bindParam(':datado', $datado, PDO::PARAM_STR);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <!--<th>Vendedor</th>-->
                                                    <th>Data/Hora</th>
                                                    <th>Descri&ccedil;&atilde;o</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                //encontrando o tipo de log
                                                if(strpos($lin->descricao,'abriu')) { $lin->descricao = str_replace('abriu','<span class="label label-info">ABRIU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'acessou')) { $lin->descricao = str_replace('acessou','<span class="label label-info">ACESSOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'adicionou')) { $lin->descricao = str_replace('adicionou','<span class="label label-primary">ADICIONOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'buscou')) { $lin->descricao = str_replace('buscou','<span class="label label-info">BUSCOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'apagou')) { $lin->descricao = str_replace('apagou','<span class="label label-danger">APAGOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'ativou')) { $lin->descricao = str_replace('ativou','<span class="label label-success">ATIVOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'atualizou')) { $lin->descricao = str_replace('atualizou','<span class="label label-primary">ATUALIZOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'cadastrou')) { $lin->descricao = str_replace('cadastrou','<span class="label label-primary">CADASTROU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'entrou')) { $lin->descricao = str_replace('entrou','<span class="label label-success">ENTROU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'fez')) { $lin->descricao = str_replace('fez','<span class="label label-success">FEZ</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'gerou')) { $lin->descricao = str_replace('gerou','<span class="label label-info">GEROU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'imprimiu')) { $lin->descricao = str_replace('imprimiu','<span class="label label-success">IMPRIMIU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'salvou')) { $lin->descricao = str_replace('salvou','<span class="label label-primary">SALVOU</span>',$lin->descricao); }
                                                if(strpos($lin->descricao,'saiu')) { $lin->descricao = str_replace('saiu','<span class="label label-success">SAIU</span>',$lin->descricao); }

                                                //invertendo a data
                                                $ano = substr($lin->datado,0,4);
                                                $mes = substr($lin->datado,5,2);
                                                $dia = substr($lin->datado,8);
                                                $lin->datado = $dia."/".$mes."/".$ano;

                                                echo'
                                                <tr>
                                                    <!--<td>'.$lin->vendedor.'</td>-->
                                                    <td>'.$lin->datado.' &#45; '.$lin->hora.' h</td>
                                                    <td>'.$lin->descricao.'</td>
                                                </tr>';

                                                unset($dia,$mes,$ano);
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <!--<th>Vendedor</th>-->
                                                    <th>Data/Hora</th>
                                                    <th>Descri&ccedil;&atilde;o</th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        unset($lin);
                                    } else {
                                        echo'
                                        <div class="callout">
                                            <h4>Nada encontrado.</h4>
                                            <p>Nenhum registro foi encontrado.</p>
                                        </div>';
                                    }
                                
                                unset($sql,$ret);
                            }
                            catch(PDOException $e) {
                                echo'Erro ao conectar o servidor '.$e->getMessage();
                            }
                        ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        </div>
        <!-- ./wrapper -->

        <script src="js/jquery-2.2.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.slimscroll.min.js"></script>
        <script src="js/fastclick.min.js"></script>
        <script src="js/smoke.min.js"></script>
        <script src="js/datepicker.min.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <script src="js/dataTables.responsive.min.js"></script>
        <script src="js/dataTables.responsive.bootstrap.min.js"></script>
        <script src="js/core.js"></script>
        <script>
            (function ($) {
                /* DATEPICKER */

                $(".date-pick").show(function () {
                    $(".date-pick").datepicker({
                        language: 'pt-BR',
                        format: "mm yyyy",
                        startView: 1,
                        minViewMode: 1
                    }).on('hide', function(e) {
                        var dt = e.target.value.split(' ');
                        location.href = "log?<?php echo $getmes; ?>=" + dt[0] + "&<?php echo $getano; ?>=" + dt[1] + "&pick=1";
                    });
                });

                /* CLEAR LOG */
                
                $('.btn-clear-log').click(function(e) {
                    e.preventDefault();
                    
                    $.smkConfirm({
                        text: 'Limpar todo o registro de log?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'appLogClear.php';
                        }
                    });
                })
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>