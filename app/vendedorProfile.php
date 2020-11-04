<?php
    require_once('appConfig.php');

    function decrypt($data, $k) {
        $l = strlen($k);
        
            if ($l < 16)
                $k = str_repeat($k, ceil(16/$l));
                $data = base64_decode($data);
                $val = openssl_decrypt($data, 'AES-256-OFB', $k, 0, $k);
        
        return $val;
    }

    $keys = base64_encode('cripta');

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
                    <h1>Meus dados</h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            include_once('appConnection.php');

                            try {
                                //buscando os dados do vendedor
                                $sql = $pdo->prepare("SELECT idvendedor,tipo,nome,usuario,senha,email FROM vendedor WHERE idvendedor = :idvendedor");
                                $sql->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        ?>
                            <form class="form-edit-vendedor">
                                <input type="hidden" name="idvendedor" id="idvendedor" value="<?php echo $lin->idvendedor; ?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?php echo $lin->tipo; ?>">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text" for="pessoa">Tipo</label>
                                            <div class="input-group col-md-12">
                                                <span class="label label-default">USU&Aacute;RIO</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="nome">Nome</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="nome" id="nome" class="form-control" value="<?php echo $lin->nome; ?>" maxlength="255" title="Informe o nome do usu&aacute;rio" placeholder="Nome do do usu&aacute;rio" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="usuario">Usu&aacute;rio</label>
                                            <div class="input-group col-md-6">
                                                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo base64_decode(decrypt($lin->usuario, $keys)); ?>" maxlength="10" title="Crie um usu&aacute;rio para acessar o sistema" placeholder="Usu&aacute;rio" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="senha">Senha</label>
                                            <div class="input-group col-md-6">
                                                <input type="password" name="senha" id="senha" class="form-control" value="<?php echo base64_decode(decrypt($lin->senha, $keys)); ?>" maxlength="10" title="Crie uma senha para acessar o sistema" placeholder="Senha" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label-nome text text-danger" for="email">Email</label>
                                            <div class="input-group col-md-12">
                                                <input type="email" name="email" id="email" class="form-control" value="<?php echo $lin->email; ?>" maxlength="100" title="Informe um email para recupera&ccedil;&atilde;o" placeholder="Email" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-flat btn-edit-vendedor">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu seus dados para edição.';

                                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                                        $res_log = $sql_log->execute();

                                            if(!$res_log) {
                                                var_dump($sql_log->errorInfo());
                                            }

                                        unset($lin,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                                    } else {
                                        echo'
                                        <div class="callout">
                                            <h4>Nada encontrado.</h4>
                                            <p>Nenhum vendedor foi encontrado.</p>
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
        <script src="js/jquery.inputmask.bundle.min.js"></script>
        <script src="js/icheck.min.js"></script>
        <script src="js/show.password.min.js"></script>
        <script src="js/core.js"></script>
        <script>
            (function ($) {
                var fade = 150, delay = 300;

                /* SHOW PASS */
                
                $("#senha").password();

                /* CRUD */

                //Edita vendedor

                $(".form-edit-vendedor").submit(function(e){
                    e.preventDefault();
                    
                    var usuario = btoa($("#usuario").val()), senha = btoa($("#senha").val());

                    $.post('vendedorUpdate.php', { rand: Math.random(),idvendedor: $("#idvendedor").val(),tipo: $("#tipo").val(),nome: $("#nome").val(),usuario: usuario,senha: senha,email: $("#email").val() }, function(data){
                        $(".btn-edit-vendedor").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Dados do vendedor editados com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='sair'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-edit-vendedor").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg,$keys); ?>