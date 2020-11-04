<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 4;
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
                        <span class="hidden-xs">Produtos</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-produto" title="Clique para cadastrar um novo produto" href="#"><i class="fa fa-tag"></i> Novo produto</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-produto" title="Clique para cadastrar um novo produto" href="#"><i class="fa fa-tag"></i> Novo produto</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            include_once('appConnection.php');

                            try {
                                //buscando os produtos
                                $monitor = 'T';
                                $sql = $pdo->prepare("SELECT idproduto,descricao,valor_custo,valor_venda FROM produto WHERE monitor = :monitor ORDER BY descricao,valor_venda");
                                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py = md5('idproduto');
                                        
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Descri&ccedil;&atilde;o</th>
                                                    <th>Valor de venda</th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                echo'
                                                <tr>
                                                    <td>'.$lin->descricao.'</td>
                                                    <td>R$ '.number_format($lin->valor_venda,2,'.',',').'</td>
                                                    <td class="td-action">
                                                        <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-produto" title="Editar o produto" href="produtoEdit.php?'.$py.'='.$lin->idproduto.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                        <span class="label label-danger"><a class="text-white a-delete-produto" id="'.$py.'-'.$lin->idproduto.'" title="Excluir o produto" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                    </td>
                                                </tr>';
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Descri&ccedil;&atilde;o</th>
                                                    <th>Valor de venda</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' acessou todos os produtos.';

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
                                            <p>Nenhum produto foi encontrado. <a class="link-new" data-toggle="modal" data-target="#modal-new-produto" title="Clique para cadastrar um novo produto" href="#">Novo produto</a></p>
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
            
            <div class="modal fade" id="modal-new-produto" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-new-produto">
                            <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
                            
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Novo produto</h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text text-danger" for="descricao">Descri&ccedil;&atilde;o</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="descricao" class="form-control" maxlength="255" title="Descri&ccedil;&atilde;o do produto" placeholder="Descri&ccedil;&atilde;o do produto" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="valor_venda">Valor de venda</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="valor_venda" id="valor_venda" class="form-control" maxlength="18" title="Valor de venda do produto" placeholder="Valor de venda" required>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-new-produto">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-produto" role="dialog" aria-hidden="true">
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

                /* MASK */
    
                $("#valor_venda").show(function(){
                    $('#valor_venda').inputmask({
                        'alias': 'numeric',
                        'groupSeparator': ',',
                        'autoGroup': true,
                        'digits': 2,
                        'digitsOptional': false,
                        'prefix': '',
                        'placeholder': '0'
                    });
                });

                /* CRUD */

                //Novo produto

                $(".form-new-produto").submit(function(e){
                    e.preventDefault();

                    $.post('produtoInsert.php', $(this).serialize(), function(data){
                        $(".btn-new-produto").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Produto cadastrado com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='produto'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-new-produto").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete produto
    
                $(".table-data").on('click', '.a-delete-produto', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        py = click[0],
                        id = click[1];
                    
                    $.smkConfirm({
                        text: 'Excluir o produto?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'produtoDelete.php?' + py + '=' + id;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>