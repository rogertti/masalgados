<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 2;
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
                        <span class="hidden-xs">Clientes</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-cliente" title="Clique para cadastrar um novo cliente" href="#"><i class="fa fa-user"></i> Novo cliente</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-cliente" title="Clique para cadastrar um novo cliente" href="#"><i class="fa fa-user"></i> Novo cliente</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            include_once('appConnection.php');

                            try {
                                $monitor = 'T';

                                    switch($_SESSION['key']) {
                                        case 'A':
                                            //buscando todos os clientes
                                            $sql = $pdo->prepare("SELECT cliente.idcliente,praca.nome AS praca,cliente.nome,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca WHERE cliente.monitor = :monitor");
                                            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                        break;
                                        case 'R':
                                            //buscando todos os clientes
                                            $sql = $pdo->prepare("SELECT cliente.idcliente,praca.nome AS praca,cliente.nome,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca WHERE cliente.monitor = :monitor");
                                            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                        break;
                                        case 'U':
                                            //buscando os clientes do vendedor logado
                                            $sql = $pdo->prepare("SELECT cliente.idcliente,praca.nome AS praca,cliente.nome,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND cliente.monitor = :monitor");
                                            $sql->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                        break;
                                    }
                
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py = md5('idcliente');
                                        
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Pra&ccedil;a</th>
                                                    <th>Nome</th>
                                                    <th>Endere&ccedil;o</th>
                                                    <th style="width: 170px;">Contato</th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                $endereco = $lin->endereco.' &#45; '.$lin->bairro.' &#45; '.$lin->cidade.' &#45; '.$lin->estado;
                                                $contato = $lin->telefone.' &#45; '.$lin->celular;

                                                if($_SESSION['key'] == 'U') {
                                                    $td_action = '
                                                    <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-cliente" title="Editar os cadastro do cliente" href="clienteEdit.php?'.$py.'='.$lin->idcliente.'"><i class="fa fa-pencil fa-lg"></i></a></span>';
                                                } else {
                                                    $td_action = '
                                                    <span class="label label-danger"><a class="text-white a-delete-cliente" id="'.$py.'-'.$lin->idcliente.'" title="Excluir o cadastro do cliente" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                    <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-cliente" title="Editar os cadastro do cliente" href="clienteEdit.php?'.$py.'='.$lin->idcliente.'"><i class="fa fa-pencil fa-lg"></i></a></span>';
                                                }

                                                echo'
                                                <tr>
                                                    <td>'.$lin->praca.'</td>
                                                    <td>'.$lin->nome.'</td>
                                                    <td>'.$endereco.'</td>
                                                    <td>'.$contato.'</td>
                                                    <td class="td-action">'.$td_action.'</td>
                                                </tr>';

                                                unset($endereco,$contato,$td_action);
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Pra&ccedil;a</th>
                                                    <th>Nome</th>
                                                    <th>Endere&ccedil;o</th>
                                                    <th>Contato</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' acessou todos os clientes.';

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
                                            <p>Nenhum cliente foi encontrado. <a class="link-new" data-toggle="modal" data-target="#modal-new-cliente" title="Clique para cadastrar um novo cliente" href="#">Novo cliente</a></p>
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
            
            <div class="modal fade" id="modal-new-cliente" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form class="form-new-cliente">
                            <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
                            
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Novo cliente</h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text text-danger" for="praca">Pra&ccedil;a</label>
                                            <?php if ($_SESSION['key'] != 'U') { ?>
                                            <div class="input-group col-md-6">
                                                <select name="praca" id="praca" class="form-control" title="Selecione a pra&ccedil;a do cliente" placeholder="Pra&ccedil;a" required>
                                                    <option value="" selected>Selecione uma praça</option>
                                                    <?php
                                                        try {
                                                            //buscando as praças
                                                            $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE praca.monitor = :monitor ORDER BY vendedor.nome,praca.nome");
                                                            $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                            $sql->execute();
                                                            $ret = $sql->rowCount();
                            
                                                                if($ret > 0) {
                                                                    while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                                        echo'<option value="'.$lin->idpraca.'">'.$lin->vendedor.'&#58; '.$lin->praca.'</option>';
                                                                    }
                                                                }
                                                            
                                                            unset($sql,$ret,$lin,$monitor);
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
                                                        $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND praca.monitor = :monitor");
                                                        $sql->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                                        $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                        $sql->execute();
                                                        $ret = $sql->rowCount();
                        
                                                            if($ret > 0) {
                                                                $lin = $sql->fetch(PDO::FETCH_OBJ);
                                                                echo'
                                                                <input type="hidden" name="praca" id="praca" value="'.$lin->idpraca.'">
                                                                <input type="text" name="fake_praca" id="fake_praca" class="form-control" value="'.$lin->praca.'" title="Pra&ccedil;a do vendedor logado" placeholder="Pra&ccedil;a" readonly>';
                                                            } else {
                                                                echo'<span class="label label-danger">O Administrador precisa cadastrar sua pra&ccedil;a</span>';
                                                            }
                                                        
                                                        unset($sql,$ret,$lin,$monitor);
                                                    }
                                                    catch(PDOException $e) {
                                                        echo'Erro ao conectar o servidor '.$e->getMessage();
                                                    }*/

                                                    //buscando as praças do vendedor logado
                                                    try {
                                                        $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND praca.monitor = :monitor ORDER BY vendedor.nome,praca.nome");
                                                        $sql->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                                        $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                        $sql->execute();
                                                        $ret = $sql->rowCount();
                        
                                                            if($ret > 0) {
                                                                echo'
                                                                <div class="input-group col-md-6">
                                                                    <select name="praca" id="praca" class="form-control" title="Selecione a pra&ccedil;a do cliente" placeholder="Pra&ccedil;a" required>
                                                                        <option value="" selected>Selecione uma praça</option>';
                                                                
                                                                    while($lin = $sql->fetch(PDO::FETCH_OBJ)){
                                                                        echo'<option value="'.$lin->idpraca.'">'.$lin->vendedor.'&#58; '.$lin->praca.'</option>';
                                                                    }

                                                                echo'
                                                                    </select>
                                                                </div>';
                                                            } else {
                                                                echo'<span class="label label-danger">O Administrador precisa cadastrar sua pra&ccedil;a</span>';
                                                            }
                                                        
                                                        unset($sql,$ret,$lin,$monitor);
                                                    }
                                                    catch(PDOException $e) {
                                                        echo'Erro ao conectar o servidor '.$e->getMessage();
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="pessoa">Pessoa</label>
                                            <div class="input-group col-md-12">
                                                <span class="form-icheck"><input type="radio" name="pessoa" id="fisica" value="F"> F&iacute;sica</span>
                                                <span class="form-icheck"><input type="radio" name="pessoa" id="juridica" value="J" checked> Jur&iacute;dica</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label-nome text text-danger" for="nome">Raz&atilde;o Social</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="nome" id="nome" class="form-control" maxlength="255" title="Informe a raz&atilde;o social do cliente" placeholder="Raz&atilde;o Social" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label-documento text text-danger" for="cnpj">CNPJ <cite class="msg-documento label label-danger"></cite></label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="cpf" id="cpf" class="form-control hide" maxlength="14" title="Informe o CPF do cliente" placeholder="CPF">
                                                <input type="text" name="cnpj" id="cnpj" class="form-control" maxlength="18" title="Informe o CNPJ do cliente" placeholder="CNPJ" required>
                                                <span class="help-block msg-cpf hide"></span>
                                                <span class="help-block msg-cnpj"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="cep">CEP <cite class="msg-cep label label-danger"></cite></label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="cep" id="cep" class="form-control" maxlength="9" title="Informe o cep do cliente" placeholder="CEP">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="endereco">Endere&ccedil;o</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="endereco" id="endereco" class="form-control" maxlength="255" title="Informe o endere&ccedil;o do cliente" placeholder="Endere&ccedil;o">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text" for="bairro">Bairro</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="bairro" id="bairro" class="form-control" maxlength="100" title="Informe o bairro do endere&ccedil;o do cliente" placeholder="Bairro">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="cidade">Cidade</label>
                                            <div class="input-group col-md-12">
                                                <input type="text" name="cidade" id="cidade" class="form-control" maxlength="100" title="Informe a cidade do endere&ccedil;o do cliente" placeholder="Cidade">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="estado">Estado</label>
                                            <div class="input-group col-md-4">
                                                <select name="estado" id="estado" class="form-control" title="Selecione o estado do endere&ccedil;o do cliente" placeholder="Estado">
                                                    <option value="AC">AC</option>
                                                    <option value="AL">AL</option>
                                                    <option value="AM">AM</option>
                                                    <option value="AP">AP</option>
                                                    <option value="BA">BA</option>
                                                    <option value="CE">CE</option>
                                                    <option value="DF">DF</option>
                                                    <option value="ES">ES</option>
                                                    <option value="GO">GO</option>
                                                    <option value="MA">MA</option>
                                                    <option value="MG">MG</option>
                                                    <option value="MS">MS</option>
                                                    <option value="MT">MT</option>
                                                    <option value="PA">PA</option>
                                                    <option value="PB">PB</option>
                                                    <option value="PE">PE</option>
                                                    <option value="PI">PI</option>
                                                    <option value="PR">PR</option>
                                                    <option value="RJ">RJ</option>
                                                    <option value="RN">RN</option>
                                                    <option value="RO">RO</option>
                                                    <option value="RR">RR</option>
                                                    <option value="RS">RS</option>
                                                    <option value="SC" selected>SC</option>
                                                    <option value="SE">SE</option>
                                                    <option value="SP">SP</option>
                                                    <option value="TO">TO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="telefone">Telefone</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="telefone" id="telefone" class="form-control" maxlength="14" title="Informe o telefone do cliente" placeholder="Telefone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="celular">Celular</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="celular" id="celular" class="form-control" maxlength="14" title="Informe o celular do cliente" placeholder="Celular">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="email">Email</label>
                                            <div class="input-group col-md-12">
                                                <input type="email" name="email" id="email" class="form-control" maxlength="100" title="Informe o email do cliente" placeholder="Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-new-cliente">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-cliente" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
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
                var fade = 150, delay = 300, showmap = false, autobusca = true;

                /* AUTOCOMPLETE */
                        
                $("#praca").show(function () {
                    /*$("#praca").select2({
                        width: 'resolve',
                        placeholder: 'Vincule uma praça ao cadastro do cliente',
                        allowClear: true
                    });*/
                });

                /* MASK */
    
                $("#cpf, #cnpj, #cep, #telefone, #celular").show(function(){
                    $('#cpf').inputmask('999.999.999-99');
                    $('#cnpj').inputmask('99.999.999/9999-99');
                    $('#cep').inputmask('99999-999');
                    $('#telefone').inputmask('(99)9999-9999');
                    $('#celular').inputmask('(99)99999-9999');
                });

                /* ICHECK */

                $("#fisica, #juridica").show(function () {
                    $("#fisica").on("ifChecked", function(event){
                        $(".label-nome").html('Nome');
                        $(".label-documento").html('CPF <cite class="msg-documento label label-danger"></cite>');

                        $("#nome").attr("placeholder", "Nome");
                        $("#cpf").attr("required", "");
                        $("#cnpj").removeAttr("required", "");

                        $("#cpf, .msg-cpf").removeClass("hide");
                        $("#cnpj, .msg-cnpj").addClass("hide");

                        $("#nome, #cnpj, .msg-cnpj").val("");
                    });

                    $("#juridica").on("ifChecked", function(event){
                        $(".label-nome").html('Razão Social');
                        $(".label-documento").html('CNPJ <cite class="msg-documento label label-danger"></cite>');

                        $("#nome").attr("placeholder", "Razão Social");
                        $("#cnpj").attr("required", "");
                        $("#cpf").removeAttr("required", "");

                        $("#cnpj, .msg-cnpj").removeClass("hide");
                        $("#cpf, .msg-cpf").addClass("hide");

                        $("#nome, #cpf, .msg-cpf").val("");
                    });
                });

                /* VALIDADORES */

                // cpf

                function validaCPF() {
                    $.post("appLoadCpf.php",{
                        cpf: $.trim($("#cpf").val())
                    }, function (data) {
                            if(data == "true") {
                                $("#cpf").css("background", "transparent");
                                $(".msg-documento").css("display", "none");
                            }
                            else {
                                $("#cpf").focus();
                                $("#cpf").css("background", "transparent");
                                $(".msg-documento").html("CPF inv&aacute;lido");
                            }
                    })
                }

                if (autobusca === true) {
                    $("#cpf").keyup(function(){
                        if($("#cpf").val().length == 14){
                            if($("#cpf").val().match(/_/g)) {
                            }
                            else {
                                validaCPF();
                                $("#cpf").css("background", "transparent url('img/rings-black.svg') right center no-repeat");
                            }
                        }
                    });
                }

                // CNPJ

                function validaCNPJ() {
                    $.post("appLoadCnpj.php",{
                        cnpj: $.trim($("#cnpj").val())
                    }, function (data) {
                            if(data == "true") {
                                $("#cnpj").css("background", "transparent");
                                $(".msg-documento").css("display", "none");
                            }
                            else {
                                $("#cnpj").focus();
                                $("#cnpj").css("background", "transparent");
                                $(".msg-documento").html("CNPJ inv&aacute;lido");
                            }
                    })
                }

                if (autobusca === true) {
                    $("#cnpj").keyup(function(){
                        if($("#cnpj").val().length == 18){
                            if($("#cnpj").val().match(/_/g)) {
                            }
                            else {
                                validaCNPJ();
                                $("#cnpj").css("background", "transparent url('img/rings-black.svg') right center no-repeat");
                            }
                        }
                    });
                }

                // CEP

                function buscaCep(showmap) {
                    $.post("appLoadCep.php",{
                        cep: $.trim($("#cep").val())
                    }, function (data) {
                        var rs = $.parseJSON(data);

                            if(rs.resultado == 1) {
                                $("#endereco").val(rs.tipo_logradouro + ' ' + rs.logradouro + ', ');
                                $("#bairro").val(rs.bairro);
                                $("#cidade").val(rs.cidade);
                                $("#estado").val(rs.uf);
                                $("#cep").css("background", "transparent");
                                $(".msg-cep").css("display", "none");
                            }
                            else {
                                $("#cep").focus();
                                $("#cep").css("background", "transparent");
                                $(".msg-cep").html("CEP inv&aacute;lido");
                            }
                    })
                }

                if (autobusca === true) {
                    $("#cep").on("keyup",function(){
                        if($("#cep").val().length >= 9){
                            if($("#cep").val().match(/_/g)) {
                            }
                            else {
                                buscaCep(showmap);
                                $("#cep").css("background","transparent url('img/rings-black.svg') right center no-repeat");
                            }
                        }
                    });
                }

                /* CRUD */

                //Novo cliente

                $(".form-new-cliente").submit(function(e){
                    e.preventDefault();

                    $.post('clienteInsert.php', $(this).serialize(), function(data){
                        $(".btn-new-cliente").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Cliente cadastrado com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='cliente'", delay);
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-new-cliente").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete cliente
    
                $(".table-data").on('click', '.a-delete-cliente', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        py = click[0],
                        id = click[1];
                    
                    $.smkConfirm({
                        text: 'Excluir o cadastro do cliente?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'clienteDelete.php?' + py + '=' + id;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>