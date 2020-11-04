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

        /* BUSCA OS DADOS DO CLIENTE */

        $py = md5('idcliente');
        $sql = $pdo->prepare("SELECT cliente.idcliente,praca.idpraca,cliente.nome,cliente.cpf_cnpj,cliente.cep,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular,cliente.email FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca WHERE cliente.idcliente = :idcliente");
        $sql->bindParam(':idcliente', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $lin = $sql->fetch(PDO::FETCH_OBJ);
?>
<form class="form-edit-cliente">
    <input type="hidden" name="idcliente" value="<?php echo $lin->idcliente; ?>">
    <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
    
    <div class="modal-header">
        <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edita cliente</h4>
    </div><!-- /.modal-header -->
    <div class="modal-body overing">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="text text-danger" for="praca">Pra&ccedil;a</label>
                    <?php if ($_SESSION['key'] != 'U') { ?>
                    <div class="input-group col-md-6">
                        <select name="praca" id="praca2" class="form-control" title="Selecione a pra&ccedil;a do cliente" placeholder="Pra&ccedil;a" required>
                            <option value="" selected>Selecione uma pra&ccedil;a</option>
                            <?php
                                try {
                                    //buscando as praças
                                    $monitor = 'T';
                                    $sql2 = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE praca.monitor = :monitor ORDER BY vendedor.nome,praca.nome");
                                    $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                    $sql2->execute();
                                    $ret2 = $sql2->rowCount();
    
                                        if($ret2 > 0) {
                                            while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                if($lin->idpraca == $lin2->idpraca) {
                                                    echo'<option value="'.$lin2->idpraca.'" selected>'.$lin2->vendedor.'&#58; '.$lin2->praca.'</option>';
                                                } else {
                                                    echo'<option value="'.$lin2->idpraca.'">'.$lin2->vendedor.'&#58; '.$lin2->praca.'</option>';
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
                    <?php
                        } else {
                            //buscando a praça do vendedor logado
                            /*try {
                                $monitor = 'T';
                                $sql2 = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND praca.monitor = :monitor");
                                $sql2->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql2->execute();
                                $ret2 = $sql2->rowCount();

                                    if($ret2 > 0) {
                                        $lin2 = $sql2->fetch(PDO::FETCH_OBJ);
                                        echo'
                                        <input type="hidden" name="praca" id="praca2" value="'.$lin2->idpraca.'">
                                        <input type="text" name="fake_praca" id="fake_praca2" class="form-control" value="'.$lin2->praca.'" title="Pra&ccedil;a do vendedor logado" placeholder="Pra&ccedil;a" readonly>';
                                    }
                                
                                unset($monitor,$sql2,$ret2,$lin2);
                            }
                            catch(PDOException $e) {
                                echo'Erro ao conectar o servidor '.$e->getMessage();
                            }*/

                            //buscando as praças do vendedor logado
                            try {
                                $monitor = 'T';
                                $sql2 = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND praca.monitor = :monitor");
                                $sql2->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql2->execute();
                                $ret2 = $sql2->rowCount();

                                    if($ret2 > 0) {
                                        echo'
                                        <div class="input-group col-md-6">
                                            <select name="praca" id="praca2" class="form-control" title="Selecione a pra&ccedil;a do cliente" placeholder="Pra&ccedil;a" required>';

                                            while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                if($lin->idpraca == $lin2->idpraca) {
                                                    echo'<option value="'.$lin2->idpraca.'" selected>'.$lin2->vendedor.'&#58; '.$lin2->praca.'</option>';
                                                } else {
                                                    echo'<option value="'.$lin2->idpraca.'">'.$lin2->vendedor.'&#58; '.$lin2->praca.'</option>';
                                                }
                                            }

                                        echo'
                                            </select>
                                        </div>';
                                    }
                                
                                unset($monitor,$sql2,$ret2,$lin2);
                            }
                            catch(PDOException $e) {
                                echo'Erro ao conectar o servidor '.$e->getMessage();
                            }
                        }
                    ?>
                </div>
                <?php
                    $documento = strlen($lin->cpf_cnpj);

                        switch($documento) {
                            case 18:
                ?>
                <div class="form-group">
                    <label class="text" for="pessoa">Pessoa</label>
                    <div class="input-group col-md-12">
                        <span class="form-icheck"><input type="radio" name="pessoa" id="fisica2" value="F"> F&iacute;sica</span>
                        <span class="form-icheck"><input type="radio" name="pessoa" id="juridica2" value="J" checked> Jur&iacute;dica</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-nome text text-danger" for="nome">Raz&atilde;o Social</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="nome" id="nome2" class="form-control" value="<?php echo $lin->nome; ?>" maxlength="255" title="Informe a raz&atilde;o social do cliente" placeholder="Raz&atilde;o Social" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-documento text text-danger" for="cnpj">CNPJ <cite class="msg-documento label label-danger"></cite></label>
                    <div class="input-group col-md-4">
                        <input type="text" name="cpf" id="cpf2" class="form-control hide" maxlength="14" title="Informe o CPF do cliente" placeholder="CPF">
                        <input type="text" name="cnpj" id="cnpj2" class="form-control" maxlength="18" value="<?php echo $lin->cpf_cnpj; ?>" title="Informe o CNPJ do cliente" placeholder="CNPJ" required>
                        <span class="help-block msg-cpf hide"></span>
                        <span class="help-block msg-cnpj"></span>
                    </div>
                </div>
                <?php
                            break;
                            case 14:
                ?>
                <div class="form-group">
                    <label class="text" for="pessoa">Pessoa</label>
                    <div class="input-group col-md-12">
                        <span class="form-icheck"><input type="radio" name="pessoa" id="fisica2" value="F" checked> F&iacute;sica</span>
                        <span class="form-icheck"><input type="radio" name="pessoa" id="juridica2" value="J"> Jur&iacute;dica</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-nome text text-danger" for="nome">Nome</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="nome" id="nome2" class="form-control" value="<?php echo $lin->nome; ?>" maxlength="255" title="Informe o nome do cliente" placeholder="Nome" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-documento text text-danger" for="cpf">CPF <cite class="msg-documento label label-danger"></cite></label>
                    <div class="input-group col-md-4">
                        <input type="text" name="cpf" id="cpf2" class="form-control" maxlength="14" value="<?php echo $lin->cpf_cnpj; ?>" title="Informe o CPF do cliente" placeholder="CPF" required>
                        <input type="text" name="cnpj" id="cnpj2" class="form-control hide" maxlength="18" title="Informe o CNPJ do cliente" placeholder="CNPJ">
                        <span class="help-block msg-cpf"></span>
                        <span class="help-block msg-cnpj hide"></span>
                    </div>
                </div>
                <?php
                            break;
                        }
                ?>
                <div class="form-group">
                    <label class="text" for="cep">CEP <cite class="msg-cep label label-danger"></cite></label>
                    <div class="input-group col-md-4">
                        <input type="text" name="cep" id="cep2" class="form-control" value="<?php echo $lin->cep; ?>" maxlength="9" title="Informe o cep do cliente" placeholder="CEP">
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="endereco">Endere&ccedil;o</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="endereco" id="endereco2" class="form-control" value="<?php echo $lin->endereco; ?>" maxlength="255" title="Informe o endere&ccedil;o do cliente" placeholder="Endere&ccedil;o">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="text" for="bairro">Bairro</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="bairro" id="bairro2" class="form-control" value="<?php echo $lin->bairro; ?>" maxlength="100" title="Informe o bairro do endere&ccedil;o do cliente" placeholder="Bairro">
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="cidade">Cidade</label>
                    <div class="input-group col-md-12">
                        <input type="text" name="cidade" id="cidade2" class="form-control" value="<?php echo $lin->cidade; ?>" maxlength="100" title="Informe a cidade do endere&ccedil;o do cliente" placeholder="Cidade">
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="estado">Estado</label>
                    <div class="input-group col-md-4">
                        <select name="estado" id="estado2" class="form-control" title="Selecione o estado do endere&ccedil;o do cliente" placeholder="Estado">
                        <?php
                            if($lin->estado == 'AC') {echo'<option value="AC" selected="selected">AC</option>';} else {echo'<option value="AC">AC</option>';}
                            if($lin->estado == 'AL') {echo'<option value="AL" selected="selected">AL</option>';} else {echo'<option value="AL">AL</option>';}
                            if($lin->estado == 'AM') {echo'<option value="AM" selected="selected">AM</option>';} else {echo'<option value="AM">AM</option>';}
                            if($lin->estado == 'AP') {echo'<option value="AP" selected="selected">AP</option>';} else {echo'<option value="AP">AP</option>';}
                            if($lin->estado == 'BA') {echo'<option value="BA" selected="selected">BA</option>';} else {echo'<option value="BA">BA</option>';}
                            if($lin->estado == 'CE') {echo'<option value="CE" selected="selected">CE</option>';} else {echo'<option value="CE">CE</option>';}
                            if($lin->estado == 'DF') {echo'<option value="DF" selected="selected">DF</option>';} else {echo'<option value="DF">DF</option>';}
                            if($lin->estado == 'ES') {echo'<option value="ES" selected="selected">ES</option>';} else {echo'<option value="ES">ES</option>';}
                            if($lin->estado == 'GO') {echo'<option value="GO" selected="selected">GO</option>';} else {echo'<option value="GO">GO</option>';}
                            if($lin->estado == 'MA') {echo'<option value="MA" selected="selected">MA</option>';} else {echo'<option value="MA">MA</option>';}
                            if($lin->estado == 'MG') {echo'<option value="MG" selected="selected">MG</option>';} else {echo'<option value="MG">MG</option>';}
                            if($lin->estado == 'MS') {echo'<option value="MS" selected="selected">MS</option>';} else {echo'<option value="MS">MS</option>';}
                            if($lin->estado == 'MT') {echo'<option value="MT" selected="selected">MT</option>';} else {echo'<option value="MT">MT</option>';}
                            if($lin->estado == 'PA') {echo'<option value="PA" selected="selected">PA</option>';} else {echo'<option value="PA">PA</option>';}
                            if($lin->estado == 'PB') {echo'<option value="PB" selected="selected">PB</option>';} else {echo'<option value="PB">PB</option>';}
                            if($lin->estado == 'PE') {echo'<option value="PE" selected="selected">PE</option>';} else {echo'<option value="PE">PE</option>';}
                            if($lin->estado == 'PI') {echo'<option value="PI" selected="selected">PI</option>';} else {echo'<option value="PI">PI</option>';}
                            if($lin->estado == 'PR') {echo'<option value="PR" selected="selected">PR</option>';} else {echo'<option value="PR">PR</option>';}
                            if($lin->estado == 'RJ') {echo'<option value="RJ" selected="selected">RJ</option>';} else {echo'<option value="RJ">RJ</option>';}
                            if($lin->estado == 'RN') {echo'<option value="RN" selected="selected">RN</option>';} else {echo'<option value="RN">RN</option>';}
                            if($lin->estado == 'RO') {echo'<option value="RO" selected="selected">RO</option>';} else {echo'<option value="RO">RO</option>';}
                            if($lin->estado == 'RR') {echo'<option value="RR" selected="selected">RR</option>';} else {echo'<option value="RR">RR</option>';}
                            if($lin->estado == 'RS') {echo'<option value="RS" selected="selected">RS</option>';} else {echo'<option value="RS">RS</option>';}
                            if($lin->estado == 'SC') {echo'<option value="SC" selected="selected">SC</option>';} else {echo'<option value="SC">SC</option>';}
                            if($lin->estado == 'SE') {echo'<option value="SE" selected="selected">SE</option>';} else {echo'<option value="SE">SE</option>';}
                            if($lin->estado == 'SP') {echo'<option value="SP" selected="selected">SP</option>';} else {echo'<option value="SP">SP</option>';}
                            if($lin->estado == 'TO') {echo'<option value="TO" selected="selected">TO</option>';} else {echo'<option value="TO">TO</option>';}
                        ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="telefone">Telefone</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="telefone" id="telefone2" class="form-control" value="<?php echo $lin->telefone; ?>" maxlength="14" title="Informe o telefone do cliente" placeholder="Telefone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="celular">Celular</label>
                    <div class="input-group col-md-4">
                        <input type="text" name="celular" id="celular2" class="form-control" value="<?php echo $lin->celular; ?>" maxlength="14" title="Informe o celular do cliente" placeholder="Celular">
                    </div>
                </div>
                <div class="form-group">
                    <label class="text" for="email">Email</label>
                    <div class="input-group col-md-12">
                        <input type="email" name="email" id="email2" class="form-control" value="<?php echo $lin->email; ?>" maxlength="100" title="Informe o email do cliente" placeholder="Email">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary btn-flat btn-edit-cliente">Salvar</button>
    </div>
</form>
<script>
    (function ($) {
        var fade = 150, delay = 300, showmap = false, autobusca = true;

        /* MODAL */
        
        $('.closed').click(function () {
            location.reload();
        });

        /* AUTOCOMPLETE */
                        
        $("#praca2").show(function () {
            /*$("#praca2").select2({
                width: 'resolve',
                placeholder: 'Vincule uma praça ao cadastro do cliente',
                allowClear: true
            });*/
        });

        /* MASK */

        $("#cpf2, #cnpj2, #cep2, #telefone2, #celular2").show(function(){
            $('#cpf2').inputmask('999.999.999-99');
            $('#cnpj2').inputmask('99.999.999/9999-99');
            $('#cep2').inputmask('99999-999');
            $('#telefone2').inputmask('(99)9999-9999');
            $('#celular2').inputmask('(99)99999-9999');
        });

        /* ICHECK */

        $("input[type='checkbox'], input[type='radio']").show(function () {
            $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        });

        $("#fisica2, #juridica2").show(function () {
            $("#fisica2").on("ifChecked", function(event){
                $(".label-nome").html('Nome');
                $(".label-documento").html('CPF <cite class="msg-documento label label-danger"></cite>');

                $("#nome2").attr("placeholder", "Nome");
                $("#cpf2").attr("required", "");
                $("#cnpj2").removeAttr("required", "");

                $("#cpf2, .msg-cpf").removeClass("hide");
                $("#cnpj2, .msg-cnpj").addClass("hide");

                $("#nome2, #cnpj2, .msg-cnpj").val("");
            });

            $("#juridica2").on("ifChecked", function(event){
                $(".label-nome").html('Razão Social');
                $(".label-documento").html('CNPJ <cite class="msg-documento label label-danger"></cite>');

                $("#nome2").attr("placeholder", "Razão Social");
                $("#cnpj2").attr("required", "");
                $("#cpf2").removeAttr("required", "");

                $("#cnpj2, .msg-cnpj").removeClass("hide");
                $("#cpf2, .msg-cpf").addClass("hide");

                $("#nome2, #cpf2, .msg-cpf").val("");
            });
        });

        /* VALIDADORES */

        // cpf

        function validaCPF() {
            $.post("appLoadCpf.php",{
                cpf: $.trim($("#cpf2").val())
            }, function (data) {
                    if(data == "true") {
                        $("#cpf2").css("background", "transparent");
                        $(".msg-documento").css("display", "none");
                    }
                    else {
                        $("#cpf2").focus();
                        $("#cpf2").css("background", "transparent");
                        $(".msg-documento").html("CPF inv&aacute;lido");
                    }
            })
        }

        if (autobusca === true) {
            $("#cpf2").keyup(function(){
                if($("#cpf2").val().length == 14){
                    if($("#cpf2").val().match(/_/g)) {
                    }
                    else {
                        validaCPF();
                        $("#cpf2").css("background", "transparent url('img/rings-black.svg') right center no-repeat");
                    }
                }
            });
        }

        // CNPJ

        function validaCNPJ() {
            $.post("appLoadCnpj.php",{
                cnpj: $.trim($("#cnpj2").val())
            }, function (data) {
                    if(data == "true") {
                        $("#cnpj2").css("background", "transparent");
                        $(".msg-documento").css("display", "none");
                    }
                    else {
                        $("#cnpj2").focus();
                        $("#cnpj2").css("background", "transparent");
                        $(".msg-documento").html("CNPJ inv&aacute;lido");
                    }
            })
        }

        if (autobusca === true) {
            $("#cnpj2").keyup(function(){
                if($("#cnpj2").val().length == 18){
                    if($("#cnpj2").val().match(/_/g)) {
                    }
                    else {
                        validaCNPJ();
                        $("#cnpj2").css("background", "transparent url('img/rings-black.svg') right center no-repeat");
                    }
                }
            });
        }

        // CEP

        function buscaCep(showmap) {
            $.post("appLoadCep.php",{
                cep: $.trim($("#cep2").val())
            }, function (data) {
                var rs = $.parseJSON(data);

                    if(rs.resultado == 1) {
                        $("#endereco2").val(rs.tipo_logradouro + ' ' + rs.logradouro + ', ');
                        $("#bairro2").val(rs.bairro);
                        $("#cidade2").val(rs.cidade);
                        $("#estado2").val(rs.uf);
                        $("#cep2").css("background", "transparent");
                        $(".msg-cep").css("display", "none");
                    }
                    else {
                        $("#cep2").focus();
                        $("#cep2").css("background", "transparent");
                        $(".msg-cep").html("CEP inv&aacute;lido");
                    }
            })
        }

        if (autobusca === true) {
            $("#cep2").on("keyup",function(){
                if($("#cep2").val().length >= 9){
                    if($("#cep2").val().match(/_/g)) {
                    }
                    else {
                        buscaCep(showmap);
                        $("#cep2").css("background","transparent url('img/rings-black.svg') right center no-repeat");
                    }
                }
            });
        }

        /* CRUD */

        //Edita cliente

        $(".form-edit-cliente").submit(function(e){
            e.preventDefault();

            $.post('clienteUpdate.php', $(this).serialize(), function(data){
                $(".btn-edit-cliente").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                switch (data) {
                case 'reload':
                    $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                    location.reload();
                    break;

                case 'true':
                    $.smkAlert({text: 'Dados do cliente editados com sucesso.', type: 'success', time: 2});
                    window.setTimeout("location.href='cliente'", delay);
                    break;

                default:
                    $.smkAlert({text: data, type: 'warning', time: 3});
                    break;
                }

                $(".btn-edit-cliente").html('Salvar').fadeTo(fade, 1);
            });

            return false;
        });
    })(jQuery);
</script>
<?php
                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu o cadastro do cliente '.$lin->nome.' para edição.';

                $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                $res_log = $sql_log->execute();

                    if(!$res_log) {
                        var_dump($sql_log->errorInfo());
                    }

                unset($lin,$documento,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
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