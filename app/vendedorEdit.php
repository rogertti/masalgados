<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* CLEAR CACHE */
    
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    //header("Content-Type: application/xml; charset=utf-8");

    function decrypt($data, $k) {
        $l = strlen($k);
        
            if ($l < 16)
                $k = str_repeat($k, ceil(16/$l));
                $data = base64_decode($data);
                $val = openssl_decrypt($data, 'AES-256-OFB', $k, 0, $k);
        
        return $val;
    }

    $keys = base64_encode('cripta');

    try {
        include_once('appConnection.php');

        /* BUSCA OS DADOS DO VENDEDOR */

        $py = md5('idvendedor');
        $sql = $pdo->prepare("SELECT idvendedor,tipo,nome,usuario,senha,email FROM vendedor WHERE vendedor.idvendedor = :idvendedor");
        $sql->bindParam(':idvendedor', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $lin = $sql->fetch(PDO::FETCH_OBJ);
?>
<form class="form-edit-vendedor">
    <input type="hidden" name="idvendedor" id="idvendedor" value="<?php echo $lin->idvendedor; ?>">
    
    <div class="modal-header">
        <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edita vendedor</h4>
    </div><!-- /.modal-header -->
    <div class="modal-body overing">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="text" for="pessoa">Tipo</label>
                    <div class="input-group col-md-12">
                    <?php
                        switch($lin->tipo) {
                            case 'A':
                            echo'
                            <span class="form-icheck"><input type="radio" name="tipo2" value="A" checked> Administrador</span>
                            <span class="form-icheck"><input type="radio" name="tipo2" value="U"> Usu&aacute;rio</span>';
                            break;

                            case 'U':
                            echo'
                            <span class="form-icheck"><input type="radio" name="tipo2" value="A"> Administrador</span>
                            <span class="form-icheck"><input type="radio" name="tipo2" value="U" checked> Usu&aacute;rio</span>';
                            break;
                        }
                    ?> 
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="nome">Nome</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="nome" id="nome2" class="form-control" value="<?php echo $lin->nome; ?>" maxlength="255" title="Informe o nome do usu&aacute;rio" placeholder="Nome do do usu&aacute;rio" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="usuario">Usu&aacute;rio</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="usuario" id="usuario2" class="form-control" value="<?php echo base64_decode(decrypt($lin->usuario, $keys)); ?>" maxlength="10" title="Crie um usu&aacute;rio para acessar o sistema" placeholder="Usu&aacute;rio" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="senha">Senha</label>
                    <div class="input-group col-md-4">
                        <input type="password" name="senha" id="senha2" class="form-control" value="<?php echo base64_decode(decrypt($lin->senha, $keys)); ?>" maxlength="10" title="Crie uma senha para acessar o sistema" placeholder="Senha" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-nome text text-danger" for="email">Email</label>
                    <div class="input-group col-md-12">
                        <input type="email" name="email" id="email2" class="form-control" value="<?php echo $lin->email; ?>" maxlength="100" title="Informe um email para recupera&ccedil;&atilde;o" placeholder="Email" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary btn-flat btn-edit-vendedor">Salvar</button>
    </div>
</form>
<script>
    (function ($) {
        var fade = 150, delay = 300;

        /* ICHECK */

        $("input[type='checkbox'], input[type='radio']").show(function () {
            $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        });

        /* SHOW PASS */
        
        $("#senha2").password();

        /* CRUD */

        //Edita vendedor

        $(".form-edit-vendedor").submit(function(e){
            e.preventDefault();
            
            var usuario = btoa($("#usuario2").val()), senha = btoa($("#senha2").val());

            $.post('vendedorUpdate.php', { rand: Math.random(),idvendedor: $("#idvendedor").val(),tipo: $("input[name='tipo2']:checked").val(),nome: $("#nome2").val(),usuario: usuario,senha: senha,email: $("#email2").val() }, function(data){
                $(".btn-edit-vendedor").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                switch (data) {
                case 'reload':
                    $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                    location.reload();
                    break;

                case 'true':
                    $.smkAlert({text: 'Dados do vendedor editados com sucesso.', type: 'success', time: 2});
                    <?php if($_SESSION['id'] == $lin->idvendedor) { ?>
                    window.setTimeout("location.href='sair'", delay);
                    <?php } else {?>
                    window.setTimeout("location.href='vendedor'", delay);
                    <?php } ?>
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
<?php
                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu o cadastro do vendedor '.$lin->nome.' para edicão.';

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
            } //if($ret > 0)
            else {
                echo'
                <div class="callout">
                    <h4>Par&acirc;mentro incorreto</h4>
                </div>';
            }

        unset($sql,$ret,$py);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }

    unset($pdo,$e);
?>