<?php
    require_once('appConfig.php');

    if(file_exists('appInstall.php')) {
        header('location:instalacao');
    }

    if(isset($_SESSION['key'])) {
        if($_SESSION['key'] == 'A') {
            header('location:inicio-adm');
        }

        if($_SESSION['key'] == 'U') {
            header('location:inicio');
        }
    }

    try {
        //verifica se a tabela vendedor está vazia
        include_once('appConnection.php');
    
        $sql = $pdo->prepare("SELECT idvendedor FROM vendedor");
        $sql->execute();
        $ret = $sql->rowCount();
        
            if($ret == 0) {
                rename('appInstallDone.php','appInstall.php');
                header('location:instalacao');
            }
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
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
        <link rel="stylesheet" href="css/core.css">
        <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><script src="js/respond.min.js"></script><![endif]-->
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <!--<a href="#"><strong><?php echo $cfg['title']; ?></strong></a>-->
                <a href="#"><strong><img src="img/logo-gray.png" title="<?php echo $cfg['title']; ?>" alt="<?php echo $cfg['title']; ?>"></strong></a>
            </div>
            <div class="login-box-body">
                <p class="login-box-msg">Entre para iniciar sua sess&atilde;o</p>

                <form class="form-login">
                    <div class="form-group has-feedback">
                        <input type="text" id="usuario" class="form-control" placeholder="Usu&aacute;rio" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="senha" class="form-control" placeholder="Senha" required>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <a data-toggle="modal" data-target="#recover-pass" href="#" title="Esqueceu a senha?">Esqueceu a senha?</a>
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat btn-login">Entrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="modal fade" id="recover-pass" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="form-recover-pass">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Recupere a sua senha</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="email"><i class="fa fa-asterisk"></i> Email</label>
                                <input type="email" name="email" id="email" class="form-control" maxlength="100" title="Digite o email cadastrado" placeholder="Digite o email cadastrado" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary btn-flat btn-recover-pass">Recuperar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->
        
        <script src="js/jquery-2.2.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/smoke.min.js"></script>
        <script>
            (function ($) {
                var fade = 150, delay = 300;
                
                //show/hide pass
                
                $("#senha").smkShowPass();
                
                //login

                $(".form-login").submit(function (e) {
                    e.preventDefault();
                    
                    var usuario = btoa($("#usuario").val()), senha = btoa($("#senha").val());

                    $.post("appLoginTrust.php", { usuario: usuario, senha: senha, rand: Math.random()}, function (data) {
                        $(".btn-login").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Redirecionando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'admin':
                            $.smkAlert({text: 'Login efetuado com sucesso.', type: 'success', time: 1});
                            window.setTimeout("location.href='inicio-adm'", delay);
                            break;
                        
                        case 'root':
                            $.smkAlert({text: 'Login efetuado com sucesso.', type: 'success', time: 1});
                            window.setTimeout("location.href='inicio-adm'", delay);
                            break;

                        case 'user':
                            $.smkAlert({text: 'Login efetuado com sucesso.', type: 'success', time: 1});
                            window.setTimeout("location.href='inicio'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-login").html('Entrar').fadeTo(fade, 1);
                    });

                    return false;
                });
                
                //recuperar a senha
                
                $(".form-recover-pass").submit(function (e) {
                    e.preventDefault();
                    
                    $.post("appLoginRecover.php", { email: $("#email").val(), rand: Math.random()}, function (data) {
                        $(".btn-recover-pass").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'true':
                            $.smkAlert({text: 'Recupera&ccedil;&atilde;o efetuada com sucesso.', type: 'success', time: 1});
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-recover-pass").html('Recuperar').fadeTo(fade, 1);
                    });
                    
                    return false;
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($cfg,$pdo,$e,$sql,$ret); ?>