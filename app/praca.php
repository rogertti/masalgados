<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 3;
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
                        <span class="hidden-xs">Pra&ccedil;as</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-praca" title="Clique para cadastrar uma nova pra&ccedil;a" href="#"><i class="fa fa-map-marker"></i> Nova pra&ccedil;a</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-praca" title="Clique para cadastrar uma nova pra&ccedil;a" href="#"><i class="fa fa-map-marker"></i> Nova pra&ccedil;a</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            include_once('appConnection.php');

                            try {
                                //buscando os pracas
                                $monitor = 'T';
                                $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE praca.monitor = :monitor ORDER BY vendedor.nome,praca.nome");
                                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py = md5('idpraca');
                                        
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Vendedor</th>
                                                    <th>Pra&ccedil;a</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                echo'
                                                <tr>
                                                    <td>'.$lin->vendedor.'</td>
                                                    <td>'.$lin->praca.'</td>
                                                    <td class="td-action">
                                                        <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-praca" title="Editar os cadastro do praca" href="pracaEdit.php?'.$py.'='.$lin->idpraca.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                        <span class="label label-danger"><a class="text-white a-delete-praca" id="'.$py.'-'.$lin->idpraca.'" title="Excluir o cadastro do praca" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                    </td>
                                                </tr>';
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Vendedor</th>
                                                    <th>Pra&ccedil;a</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' acessou todas as praças.';

                                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                                        $res_log = $sql_log->execute();

                                            if(!$res_log) {
                                                var_dump($sql_log->errorInfo());
                                            }

                                        unset($py,$lin,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                                    } else {
                                        echo'
                                        <div class="callout">
                                            <h4>Nada encontrado.</h4>
                                            <p>Nenhum praca foi encontrado. <a class="link-new" data-toggle="modal" data-target="#modal-new-praca" title="Clique para cadastrar uma nova pra&ccedil;a" href="#">Nova pra&ccedil;a</a></p>
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
            
            <div class="modal fade" id="modal-new-praca" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-new-praca">
                            <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
                            
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Nova pra&ccedil;a</h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text text-danger" for="vendedor">Vendedor</label>
                                            <div class="input-group col-md-12">
                                                <select name="vendedor" id="vendedor" class="form-control" title="Vincule o vendedor" placeholder="Vendedor" required>
                                                    <option value="" selected>Vincule um vendedor a pra&ccedil;a</option>
                                                    <?php
                                                        try {
                                                            //buscando os vendedores
                                                            $sql2 = $pdo->prepare("SELECT idvendedor,nome AS vendedor FROM vendedor WHERE monitor = :monitor ORDER BY nome");
                                                            $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                            $sql2->execute();
                                                            $ret2 = $sql2->rowCount();
                            
                                                                if($ret2 > 0) {
                                                                    while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                                        echo'<option value="'.$lin2->idvendedor.'">'.$lin2->vendedor.'</option>';
                                                                    }
                                                                }
                                                            
                                                            unset($monitor,$sql2,$ret2,$lin2);
                                                        }
                                                        catch(PDOException $e) {
                                                            echo'Erro ao conectar o servidor '.$e->getMessage();
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="nome">Nome</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="nome" class="form-control" maxlength="255" title="Nome da pra&ccedil;a" placeholder="Pra&ccedil;a" required>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-new-praca">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-praca" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content"></div>
                </div>
            </div>
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
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <script src="js/dataTables.responsive.min.js"></script>
        <script src="js/dataTables.responsive.bootstrap.min.js"></script>
        <script src="js/core.js"></script>
        <script>
            (function ($) {
                var fade = 150, delay = 300;

                /* CRUD */

                //Nova praca

                $(".form-new-praca").submit(function(e){
                    e.preventDefault();

                    $.post('pracaInsert.php', $(this).serialize(), function(data){
                        $(".btn-new-praca").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Praça cadastrada com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='praca'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-new-praca").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete praca
    
                $(".table-data").on('click', '.a-delete-praca', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        py = click[0],
                        id = click[1];
                    
                    $.smkConfirm({
                        text: 'Excluir a praça cadastrada?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'pracaDelete.php?' + py + '=' + id;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>