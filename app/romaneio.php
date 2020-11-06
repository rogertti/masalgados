<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 5;
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
        <link rel="stylesheet" href="css/select2.min.css">
        <link rel="stylesheet" href="css/icheck.min.css">
        <link rel="stylesheet" href="css/daterangepicker.min.css">
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
                    <h1>Romaneios</h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline form-view-romaneio">
                                <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">

                                <div class="form-group" style="margin-right: 25px;">
                                    <label class="text text-danger" for="vendedor">Vendedor</label>
                                    <?php if($_SESSION['key'] != 'U') { ?>
                                    <select name="vendedor" id="vendedor" class="form-control" title="Selecione o vendedor" placeholder="Vendedor" required>
                                        <option value="" selected>Selecione o vendedor</option>
                                        <?php
                                            try {
                                                include_once('appConnection.php');

                                                //buscando os vendedores
                                                $monitor = 'T';
                                                $sql2 = $pdo->prepare("SELECT idvendedor,nome AS vendedor FROM vendedor WHERE monitor = :monitor ORDER BY nome");
                                                $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                $sql2->execute();
                                                $ret2 = $sql2->rowCount();
                
                                                    if($ret2 > 0) {
                                                        while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                            echo'<option value="'.$lin2->idvendedor.'-'.$lin2->vendedor.'">'.$lin2->vendedor.'</option>';
                                                        }
                                                    }
                                                
                                                unset($sql2,$ret2,$lin2,$monitor);
                                            }
                                            catch(PDOException $e) {
                                                echo'Erro ao conectar o servidor '.$e->getMessage();
                                            }
                                        ?>
                                    </select>
                                    <?php } else { ?>
                                    <input type="hidden" name="vendedor" id="vendedor" value="<?php echo $_SESSION['id'].'-'.$_SESSION['seller']; ?>">
                                    <input type="text" name="fake_vendedor" id="fake_vendedor" class="form-control" value="<?php echo $_SESSION['seller']; ?>" title="Vendedor" placeholder="Vendedor" readonly>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label class="text text-danger" for="periodo">Per&iacute;odo</label>
                                    <input type="text" name="periodo" id="periodo" class="form-control" title="Selecione o intervalo de datas" placeholder="Per&iacute;odo" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-flat btn-view-romaneio">Gerar</button>
                                </div>
                            </form>
                            
                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="div-view-romaneio"></div>
                                </div>
                            </div>
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
        <script src="js/jquery.inputmask.bundle.min.js"></script>
        <script src="js/icheck.min.js"></script>
        <script src="js/select2.full.min.js"></script>
        <script src="js/moment.min.js"></script>
        <script src="js/daterangepicker.min.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <script src="js/dataTables.responsive.min.js"></script>
        <script src="js/dataTables.responsive.bootstrap.min.js"></script>
        <script src="js/core.js"></script>
        <script>
            (function ($) {
                var fade = 150, delay = 300;

                /* DATE RANGE PICKER */
    
                $("#periodo").show(function() {
                    $("#periodo").daterangepicker({
                        autoUpdateInput: false,
                        locale: {
                            format: 'DD/MM/YYYY',
                            applyLabel: 'Aplicar',
                            cancelLabel: 'Cancelar'
                        }
                    });
                    
                    $('#periodo').on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    });

                    $('#periodo').on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });
                });

                /* CRUD */

                //View romaneio

                $(".form-view-romaneio").submit(function(e){
                    e.preventDefault();

                    $.post('romaneioView.php', $(this).serialize(), function(data) {
                        $(".btn-view-romaneio").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'null':
                            $.smkAlert({text: 'Variável de controle nula.', type: 'danger', time: 2});
                            break;

                        default:
                            $.smkAlert({text: 'Romaneio gerado com sucesso.', type: 'success', time: 3});
                            $('.div-view-romaneio').html(data);
                            $('.box-body').find('form')[0].reset();
                            break;
                        }

                        $(".btn-view-romaneio").html('Gerar').fadeTo(fade, 1);
                    });

                    return false;
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>