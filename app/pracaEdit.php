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

    try {
        include_once('appConnection.php');

        /* BUSCA OS DADOS DA PRACA */

        $py = md5('idpraca');
        $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome,vendedor.idvendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE praca.idpraca = :idpraca");
        $sql->bindParam(':idpraca', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $lin = $sql->fetch(PDO::FETCH_OBJ);
?>
<form class="form-edit-praca">
    <input type="hidden" name="idpraca" value="<?php echo $lin->idpraca; ?>">
    <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
    
    <div class="modal-header">
        <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edita pra&ccedil;a</h4>
    </div><!-- /.modal-header -->
    <div class="modal-body overing">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="text text-danger" for="vendedor">Vendedor</label>
                    <div class="input-group col-md-12">
                        <select name="vendedor" id="vendedor2" class="form-control" title="Vincule o vendedor" placeholder="Vendedor" required>
                            <option value="" selected>Vincule um vendedor a pra&ccedil;a</option>
                            <?php
                                try {
                                    //buscando os vendedores
                                    $monitor = 'T';
                                    $sql2 = $pdo->prepare("SELECT idvendedor,nome AS vendedor FROM vendedor WHERE monitor = :monitor ORDER BY nome");
                                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                    $sql2->execute();
                                    $ret2 = $sql2->rowCount();
    
                                        if($ret2 > 0) {
                                            while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                if($lin->idvendedor == $lin2->idvendedor) {
                                                    echo'<option value="'.$lin2->idvendedor.'" selected>'.$lin2->vendedor.'</option>';
                                                } else {
                                                    echo'<option value="'.$lin2->idvendedor.'">'.$lin2->vendedor.'</option>';
                                                }
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
                        <input type="text" name="nome" value="<?php echo $lin->nome; ?>" class="form-control" maxlength="255" title="Nome da pra&ccedil;a" placeholder="Pra&ccedil;a" required>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary btn-flat btn-edit-praca">Salvar</button>
    </div>
</form>
<script>
    (function ($) {
        var fade = 150, delay = 300;

        /* CRUD */

        //Edita praca

        $(".form-edit-praca").submit(function(e){
            e.preventDefault();

            $.post('pracaUpdate.php', $(this).serialize(), function(data){
                $(".btn-edit-praca").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                switch (data) {
                case 'reload':
                    $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                    location.reload();
                    break;

                case 'true':
                    $.smkAlert({text: 'Praça editada com sucesso.', type: 'success', time: 2});
                    window.setTimeout("location.href='praca'", delay);
                    break;

                default:
                    $.smkAlert({text: data, type: 'warning', time: 3});
                    break;
                }

                $(".btn-edit-praca").html('Salvar').fadeTo(fade, 1);
            });

            return false;
        });
    })(jQuery);
</script>
<?php
                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu a praça '.$lin->nome.' para edição.';

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