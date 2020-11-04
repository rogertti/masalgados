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

        /* BUSCA OS DADOS DO PEDIDO */

        $py = md5('idpedido');
        $sql = $pdo->prepare("SELECT idpedido,vendedor_idvendedor AS idvendedor,cliente_idcliente AS idcliente,tipo,codigo,datado,hora,desconto,forma_pagamento FROM pedido WHERE idpedido = :idpedido");
        $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $lin = $sql->fetch(PDO::FETCH_OBJ);

                //invertendo a data
                $ano = substr($lin->datado,0,4);
                $mes = substr($lin->datado,5,2);
                $dia = substr($lin->datado,8);
                $lin->datado = $dia."/".$mes."/".$ano;
?>
<form class="form-edit-pedido">
    <input type="hidden" name="idpedido" value="<?php echo $lin->idpedido; ?>">
    <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">

    <div class="modal-header">
        <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
            <span class="hidden-xs">Editar or&ccedil;amento&#47;pedido</span>
            <span class="hidden-xs pull-right"><strong><?php echo $lin->codigo; ?></strong></span>
            <span class="hidden-sm hidden-md hidden-lg"><strong><?php echo $lin->codigo; ?></strong></span>
        </h4>
    </div><!-- /.modal-header -->
    <div class="modal-body overing">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="text text-danger" for="vendedor">Vendedor</label>
                    <div class="input-group col-md-12">
                    
                    <?php
                        if($_SESSION['key'] == 'A') {
                    ?>

                        <select name="vendedor" id="vendedor2" class="form-control" title="Vincule o vendedor" placeholder="Vendedor" required>
                            <option value="" selected>Vincule um vendedor ao orçamento/pedido</option>
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
                                    
                                    unset($sql2,$ret2,$lin2);
                                }
                                catch(PDOException $e) {
                                    echo'Erro ao conectar o servidor '.$e->getMessage();
                                }
                            ?>
                        </select>
                    
                    <?php
                        } elseif($_SESSION['key'] == 'R') {
                    ?>

                        <select name="vendedor" id="vendedor2" class="form-control" title="Vincule o vendedor" placeholder="Vendedor" required>
                            <option value="" selected>Vincule um vendedor ao orçamento/pedido</option>
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
                                    
                                    unset($sql2,$ret2,$lin2);
                                }
                                catch(PDOException $e) {
                                    echo'Erro ao conectar o servidor '.$e->getMessage();
                                }
                            ?>
                        </select>

                    <?php
                        } elseif($_SESSION['key'] == 'U') {
                            try {
                                //buscando o vendedor
                                $monitor = 'T';
                                $sql2 = $pdo->prepare("SELECT idvendedor,nome AS vendedor FROM vendedor WHERE monitor = :monitor ORDER BY nome");
                                $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql2->execute();
                                $ret2 = $sql2->rowCount();

                                    if($ret2 > 0) {
                                        while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                            if($lin->idvendedor == $lin2->idvendedor) {
                                                echo'
                                                <input type="hidden" name="vendedor" value="'.$lin2->idvendedor.'">
                                                <input type="text" name="vendedor_vinculado" id="vendedor2" class="form-control" value="'.$lin2->vendedor.'" title="Vendedor vinculado" placeholder="Vendedor" readonly>';
                                            }
                                        }
                                    }
                                
                                unset($sql2,$ret2,$lin2);
                            }
                            catch(PDOException $e) {
                                echo'Erro ao conectar o servidor '.$e->getMessage();
                            }
                        }
                    ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="cliente">Cliente</label>
                    <div class="input-group col-md-12">
                        <select name="cliente" id="cliente2" class="" title="Vincule o cliente" placeholder="Cliente" style="max-width: 100%;width: 100%;" required>
                            <option value="" selected>Vincule um cliente ao orçamento/pedido</option>
                            <?php
                                try {
                                    //buscando os clientes
                                    $sql2 = $pdo->prepare("SELECT idcliente,nome AS cliente FROM cliente WHERE monitor = :monitor ORDER BY nome");
                                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                    $sql2->execute();
                                    $ret2 = $sql2->rowCount();
    
                                        if($ret2 > 0) {
                                            while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                if($lin->idcliente == $lin2->idcliente) {
                                                    echo'<option value="'.$lin2->idcliente.'" selected>'.$lin2->cliente.'</option>';
                                                } else {
                                                    echo'<option value="'.$lin2->idcliente.'">'.$lin2->cliente.'</option>';
                                                }
                                            }
                                        }
                                    
                                    unset($sql2,$ret2,$lin2,$monitor);
                                }
                                catch(PDOException $e) {
                                    echo'Erro ao conectar o servidor '.$e->getMessage();
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="Tipo">Tipo</label>
                    <div class="input-group col-md-12">
                    <?php
                        switch($lin->tipo) {
                            case 'O':
                            echo'
                            <span class="form-icheck"><input type="radio" name="tipo" value="O" checked> Or&ccedil;amento</span>
                            <span class="form-icheck"><input type="radio" name="tipo" value="P"> Pedido</span>';
                            break;

                            case 'P':
                            echo'
                            <span class="form-icheck"><input type="radio" name="tipo" value="O"> Or&ccedil;amento</span>
                            <span class="form-icheck"><input type="radio" name="tipo" value="P" checked> Pedido</span>';
                            break;
                        }
                    ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="datado">Data</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="datado" id="datado2" class="form-control" value="<?php echo $lin->datado; ?>" maxlength="10" title="Informe o celular do cliente" placeholder="Data" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text text-danger" for="hora">Hora</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="hora" id="hora2" class="form-control" value="<?php echo $lin->hora; ?>" maxlength="8" title="Informe o celular do cliente" placeholder="Hora" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary btn-flat btn-edit-pedido">Salvar</button>
    </div>
</form>
<script>
    (function ($) {
        var fade = 150, delay = 300;

        /* DATEPICKER */
    
        $("#datado2").show(function () {
            $("#datado2").datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                autoclose: true
            });
        });

        /* MASK */

        $("#hora2").show(function(){
            $('#hora2').inputmask('99:99:99');
        });

        /* ICHECK */

        $("input[type='radio']").show(function () {
            $("input[type='radio']").iCheck({
                radioClass: 'iradio_minimal'
            });
        });
        
        /* AUTOCOMPLETE */
                
        $("#cliente2").show(function () {
            $("#cliente2").select2({
                placeholder: "Vincule um cliente ao orçamento/pedido",
                allowClear: true
            });
        });

        /* CRUD */

        //Edita pedido

        $(".form-edit-pedido").submit(function(e){
            e.preventDefault();

            $.post('pedidoUpdate.php', $(this).serialize(), function(data){
                $(".btn-edit-pedido").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                if(data == 'reload'){
                    $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                    location.reload();
                }else if(data == 'true'){
                    $.smkAlert({text: 'Orçamento/Pedido editado com sucesso.', type: 'success', time: 2});
                    <?php if($_SESSION['key'] == 'A') { ?>
                    window.setTimeout("location.href='inicio-adm'", delay);
                    <?php } elseif($_SESSION['key'] == 'U') { ?>
                    window.setTimeout("location.href='inicio'", delay);
                    <?php } ?>
                }else if(data.match(/<url>/g)){
                    $.smkAlert({text: 'Orçamento/Pedido editado com sucesso.', type: 'success', time: 2});
                    data = data.replace("<url>","");
                    data = data.replace("</url>","");
                    window.setTimeout("location.href='" + data + "'", delay);
                }else{
                    $.smkAlert({text: data, type: 'warning', time: 3});
                }

                $(".btn-edit-pedido").html('Salvar').fadeTo(fade, 1);
            });

            return false;
        });
    })(jQuery);
</script>
<?php
                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu o orçamento/pedido '.$lin->codigo.' para edição.';

                $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                $res_log = $sql_log->execute();

                    if(!$res_log) {
                        var_dump($sql_log->errorInfo());
                    }

                unset($dia,$mes,$ano,$lin,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora); 
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