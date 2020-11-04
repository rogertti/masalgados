<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 6;
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
                        <span class="hidden-xs">Vendedores</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-vendedor" title="Clique para cadastrar um novo vendedor" href="#"><i class="fa fa-user"></i> Novo vendedor</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-vendedor" title="Clique para cadastrar um novo vendedor" href="#"><i class="fa fa-user"></i> Novo vendedor</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            include_once('appConnection.php');

                            try {
                                //buscando os vendedores
                                $monitor = 'T';
                                $sql = $pdo->prepare("SELECT idvendedor,tipo,nome,usuario,senha,email FROM vendedor WHERE monitor = :monitor ORDER BY nome,tipo,usuario");
                                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py = md5('idvendedor');
                                        
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Email</th>
                                                    <th>Tipo</th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                //Tratamento diferente para ROOT
                                                if($_SESSION['key'] != 'R') {
                                                    //Usuário root não é listado
                                                    if($lin->tipo != 'R') {
                                                        if($_SESSION['id'] == $lin->idvendedor) {
                                                            $tr = '<tr class="info">';
                                                            $unlock = '<span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-vendedor" title="Editar os dados do vendedor" href="vendedorEdit.php?'.$py.'='.$lin->idvendedor.'"><i class="fa fa-pencil fa-lg"></i></a></span>';
                                                        } else {
                                                            $tr = '<tr>';
                                                            $unlock = '<span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-vendedor" title="Editar os dados do vendedor" href="vendedorEdit.php?'.$py.'='.$lin->idvendedor.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                            <!--<span class="label label-danger"><a class="text-white a-delete-vendedor" id="'.$py.'-'.$lin->idvendedor.'" title="Excluir o cadastro do vendedor" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>-->';
                                                        }
        
                                                        switch($lin->tipo) {
                                                            case 'A': $lin->tipo = '<span class="label label-primary">ADMINISTRADOR</span>'; break;
                                                            case 'U': $lin->tipo = '<span class="label label-default">USU&Aacute;RIO</span>'; break;
                                                        }
        
                                                        echo'
                                                        '.$tr.'
                                                            <td>'.$lin->nome.'</td>
                                                            <td>'.$lin->email.'</td>
                                                            <td>'.$lin->tipo.'</td>
                                                            <td class="text-center">
                                                                '.$unlock.'
                                                            </td>
                                                        </tr>';
        
                                                        unset($tr,$unlock);
                                                    }
                                                } else {
                                                    switch($lin->tipo) {
                                                        case 'A': $lin->tipo = '<span class="label label-primary">ADMINISTRADOR</span>'; break;
                                                        case 'R': $lin->tipo = '<span class="label label-info">ROOT</span>'; break;
                                                        case 'U': $lin->tipo = '<span class="label label-default">USU&Aacute;RIO</span>'; break;
                                                    }

                                                    echo'
                                                    <tr>
                                                        <td>'.$lin->nome.'</td>
                                                        <td>'.$lin->email.'</td>
                                                        <td>'.$lin->tipo.'</td>
                                                        <td class="td-action">
                                                            <span class="label label-danger"><a class="text-white a-delete-vendedor" id="'.$py.'-'.$lin->idvendedor.'" title="Excluir o cadastro do vendedor" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-vendedor" title="Editar os dados do vendedor" href="vendedorEdit.php?'.$py.'='.$lin->idvendedor.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                        </td>
                                                    </tr>';
                                                }
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Email</th>
                                                    <th>Tipo</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' acessou todos os vendedores.';

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
                                            <p>Nenhum vendedor foi encontrado. <a class="link-new" data-toggle="modal" data-target="#modal-new-vendedor" title="Clique para cadastrar um novo vendedor" href="#">Novo vendedor</a></p>
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
            
            <div class="modal fade" id="modal-new-vendedor" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-new-vendedor">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Novo vendedor</h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text" for="pessoa">Tipo</label>
                                            <div class="input-group col-md-12">
                                                <span class="form-icheck"><input type="radio" name="tipo" value="A"> Administrador</span>
                                                <span class="form-icheck"><input type="radio" name="tipo" value="U" checked> Usu&aacute;rio</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="nome">Nome</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="nome" id="nome" class="form-control" maxlength="255" title="Informe o nome do usu&aacute;rio" placeholder="Nome do do usu&aacute;rio" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="usuario">Usu&aacute;rio</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="usuario" id="usuario" class="form-control" maxlength="10" title="Crie um usu&aacute;rio para acessar o sistema" placeholder="Usu&aacute;rio" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="senha">Senha</label>
                                            <div class="input-group col-md-4">
                                                <input type="password" name="senha" id="senha" class="form-control" maxlength="10" title="Crie uma senha para acessar o sistema" placeholder="Senha" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label-nome text text-danger" for="email">Email</label>
                                            <div class="input-group col-md-12">
                                                <input type="email" name="email" id="email" class="form-control" maxlength="100" title="Informe um email para recupera&ccedil;&atilde;o" placeholder="Email" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-new-vendedor">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-vendedor" role="dialog" aria-hidden="true">
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
        <script src="js/show.password.min.js"></script>
        <script src="js/core.js"></script>
        <script>
            (function ($) {
                var fade = 150, delay = 300;

                /* SHOW PASS */
                
                $("#senha").password();

                /* CRUD */

                //Novo vendedor

                $(".form-new-vendedor").submit(function(e){
                    e.preventDefault();
                    
                    var usuario = btoa($("#usuario").val()), senha = btoa($("#senha").val());

                    $.post('vendedorInsert.php', { rand: Math.random(),tipo: $("input[name='tipo']:checked").val(),nome: $("#nome").val(),usuario: usuario,senha: senha,email: $("#email").val() }, function(data){
                        $(".btn-new-vendedor").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Vendedor cadastrado com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='vendedor'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-new-vendedor").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete vendedor
    
                $(".table-data").on('click', '.a-delete-vendedor', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        py = click[0],
                        id = click[1];
                    
                    $.smkConfirm({
                        text: 'Excluir o cadastro do vendedor?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'vendedorDelete.php?' + py + '=' + id;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg,$keys); ?>