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

        /* BUSCA OS DADOS DO PRODUTO */

        $py = md5('idproduto');
        $sql = $pdo->prepare("SELECT idproduto,descricao,valor_custo,valor_venda FROM produto WHERE idproduto = :idproduto");
        $sql->bindParam(':idproduto', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $lin = $sql->fetch(PDO::FETCH_OBJ);
?>
<form class="form-edit-produto">
    <input type="hidden" name="idproduto" value="<?php echo $lin->idproduto; ?>">
    <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
    
    <div class="modal-header">
        <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edita produto</h4>
    </div><!-- /.modal-header -->
    <div class="modal-body overing">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="text text-danger" for="descricao">Descri&ccedil;&atilde;o</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="descricao" class="form-control" value="<?php echo $lin->descricao; ?>" maxlength="255" title="Descri&ccedil;&atilde;o do produto" placeholder="Descri&ccedil;&atilde;o do produto" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="valor_venda">Valor de venda</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="valor_venda" id="valor_venda2" class="form-control" value="<?php echo $lin->valor_venda; ?>" maxlength="18" title="Valor de venda do produto" placeholder="Valor de venda" required>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary btn-flat btn-edit-produto">Salvar</button>
    </div>
</form>
<script>
    (function ($) {
        var fade = 150, delay = 300;

        /* MASK */

        $("#valor_venda2").show(function(){
            $('#valor_venda2').inputmask({
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

        //Edita produto

        $(".form-edit-produto").submit(function(e){
            e.preventDefault();

            $.post('produtoUpdate.php', $(this).serialize(), function(data){
                $(".btn-edit-produto").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                switch (data) {
                case 'reload':
                    $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                    location.reload();
                    break;

                case 'true':
                    $.smkAlert({text: 'Produto editado com sucesso.', type: 'success', time: 2});
                    window.setTimeout("location.href='produto'", delay);
                    break;

                default:
                    $.smkAlert({text: data, type: 'warning', time: 3});
                    break;
                }

                $(".btn-edit-produto").html('Salvar').fadeTo(fade, 1);
            });

            return false;
        });
    })(jQuery);
</script>
<?php
                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu o produto '.$lin->descricao.' para edição.';

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