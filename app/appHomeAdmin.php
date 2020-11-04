<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 1;

    $getmes = md5('mes');
    $getano = md5('ano');

        if(isset($_GET[''.$getmes.''])) {
            $mes = $_GET[''.$getmes.''];
        } else {
            $mes = date('m');
        }

        if(isset($_GET['left'])) {
            if($mes == '12') {
                $ano = $_GET[''.$getano.''] - 1;
            } else {
                $ano = $_GET[''.$getano.''];
            }
        }

        if(isset($_GET['right'])) {
            if($mes == '01') {
                $ano = $_GET[''.$getano.''] + 1;
            } else {
                $ano = $_GET[''.$getano.''];
            }
        }

        if(isset($_GET['pick'])) {
            $ano = $_GET[''.$getano.''];
        }

        if ((!isset($_GET['left'])) and (!isset($_GET['right'])) and (!isset($_GET['pick']))) {
            $ano = date('Y');
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
        <link rel="stylesheet" href="css/datepicker.min.css">
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
                        <span class="hidden-xs">Or&ccedil;amentos/pedidos</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-pedido" title="Clique para cadastrar um novo or&ccedil;amento&#47;pedido" href="#"><i class="fa fa-file-text"></i> Novo or&ccedil;amento&#47;pedido</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-new-pedido" title="Clique para cadastrar um novo pedido" href="#"><i class="fa fa-file-text"></i> Novo or&ccedil;amento&#47;pedido</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            function mes_extenso ($fmes) {
                                switch ($fmes) {
                                    case '01': $fmes = 'Janeiro'; break;
                                    case '02': $fmes = 'Fevereiro'; break;
                                    case '03': $fmes = 'Mar&ccedil;o'; break;
                                    case '04': $fmes = 'Abril'; break;
                                    case '05': $fmes = 'Maio'; break;
                                    case '06': $fmes = 'Junho'; break;
                                    case '07': $fmes = 'Julho'; break;
                                    case '08': $fmes = 'Agosto'; break;
                                    case '09': $fmes = 'Setembro'; break;
                                    case '10': $fmes = 'Outubro'; break;
                                    case '11': $fmes = 'Novembro'; break;
                                    case '12': $fmes = 'Dezembro'; break;
                                }

                                return $fmes;
                            }
                            
                            $mesleft = $mes - 1;
                            $mesright = $mes + 1;

                                if(strlen($mesleft) == 1) {
                                    $mesleft = '0'.$mesleft;

                                        if($mesleft == '00') {
                                            $mesleft = '12';
                                        }
                                }

                                if(strlen($mesright) == 1) {
                                    $mesright = '0'.$mesright;

                                        if($mesright == '13') {
                                            $mesright = '01';
                                        }
                                } else {
                                    if($mesright == '13') {
                                        $mesright = '01';
                                    }
                                }

                            echo'
                            <div class="div-time">
                                <div class="div-time-left text-center">
                                    <a class="lead" href="inicio-adm?'.$getmes.'='.$mesleft.'&'.$getano.'='.$ano.'&left=1" title="M&ecirc;s anterior">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>
                                </div>
                                <div class="div-time-center">
                                    <p class="lead text-center">
                                        <!--<span class="text-bold text-uppercase">'.mes_extenso($mes).' de '.$ano.'</span>-->
                                        <input type="text" class="date-pick text-center" value="'.mes_extenso($mes).' de '.$ano.'" readonly>
                                    </p>
                                </div>
                                <div class="div-time-right text-center">
                                    <a class="lead" href="inicio-adm?'.$getmes.'='.$mesright.'&'.$getano.'='.$ano.'&right=1" title="Pr&oacute;ximo m&ecirc;s">
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <hr>';

                            try {
                                include_once('appConnection.php');
                                
                                //buscando os orçamentos/pedidos
                                $datado = $ano.'-'.$mes.'%';
                                $monitor = 'T';
                                #$sql = $pdo->prepare("SELECT pedido.idpedido,pedido.codigo,pedido.tipo,pedido.datado,pedido.hora,vendedor.nome AS vendedor,cliente.nome AS cliente FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente WHERE pedido.monitor = :monitor ORDER BY pedido.tipo,pedido.datado,pedido.hora,vendedor.nome,cliente.nome");
                                $sql = $pdo->prepare("SELECT pedido.idpedido,pedido.codigo,pedido.tipo,pedido.datado,pedido.hora,vendedor.nome AS vendedor,cliente.nome AS cliente FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente WHERE pedido.datado LIKE :datado AND pedido.monitor = :monitor ORDER BY pedido.tipo,pedido.datado,pedido.hora,vendedor.nome,cliente.nome");
                                $sql->bindParam(':datado', $datado, PDO::PARAM_STR);
                                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py = md5('idpedido');
                                        
                                        echo'
                                        <table class="table table-striped table-bordered table-hover table-data dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>C&oacute;digo</th>
                                                    <th>Data/Hora</th>
                                                    <th>Vendedor</th>
                                                    <th>Cliente</th>
                                                    <th style="width: 150px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                switch($lin->tipo) {
                                                    case 'O': $lin->tipo = '<span class="label label-default">OR&Ccedil;AMENTO</span>'; break;
                                                    case 'P': $lin->tipo = '<span class="label label-primary">PEDIDO</span>'; break;
                                                }

                                                //invertendo a data
                                                $ano = substr($lin->datado,0,4);
                                                $mes = substr($lin->datado,5,2);
                                                $dia = substr($lin->datado,8);
                                                $lin->datado = $dia."/".$mes."/".$ano;

                                                echo'
                                                <tr>
                                                    <td>'.$lin->tipo.'</td>
                                                    <td>'.$lin->codigo.'</td>
                                                    <td>'.$lin->datado.' &#45; '.$lin->hora.' h</td>
                                                    <td>'.$lin->vendedor.'</td>
                                                    <td>'.$lin->cliente.'</td>
                                                    <td class="td-action">
                                                        <span class="hidden-xs"><input type="checkbox" name="box_option[]" class="box-option" value="'.$lin->idpedido.'"></span>
                                                        <span class="label label-info"><a class="text-white" title="Imprimir o or&ccedil;amento/pedido" href="pedidoProdutoPrint.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-print fa-lg"></i></a></span>    
                                                        <span class="label label-primary"><a class="text-white" title="Editar os produtos do or&ccedil;amento/pedido" href="pedidoProduto.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-tag fa-lg"></i></a></span>
                                                        <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-pedido" title="Editar o or&ccedil;amento/pedido" href="pedidoEdit.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                        <span class="label label-danger"><a class="text-white a-delete-pedido" id="'.$py.'-'.$lin->idpedido.'" title="Excluir o or&ccedil;amento/pedido" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                    </td>
                                                </tr>';

                                                unset($dia,$mes,$ano);
                                            }
                                        
                                        echo'
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>C&oacute;digo</th>
                                                    <th>Data/Hora</th>
                                                    <th>Vendedor</th>
                                                    <th>Cliente</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' acessou todos os pedidos.';

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
                                            <p>Nenhum registro foi encontrado. <a class="link-new" data-toggle="modal" data-target="#modal-new-pedido" title="Clique para cadastrar uma novo pedido" href="#">Novo or&ccedil;amento&#47;pedido</a></p>
                                        </div>';
                                    }
                                
                                unset($sql,$ret,$datado);
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
            
            <div class="box-btn-option">
                <button type="button" class="btn btn-flat btn-primary btn-box-unselect-all hide"><i class="fa fa-check"></i> <span class="hidden-xs">Desmarcar todos</span></button>
                <button type="button" class="btn btn-flat btn-primary btn-box-select-all hide"><i class="fa fa-check"></i> <span class="hidden-xs">Marcar todos</span></button>
                <button type="button" class="btn btn-flat btn-success btn-box-print hide"><i class="fa fa-print"></i> <span class="hidden-xs">Imprimir selecionados</span></button>
                <button type="button" class="btn btn-flat btn-danger btn-box-delete hide"><i class="fa fa-trash-o"></i> <span class="hidden-xs">Excluir selecionados</span></button>
            </div>

            <div class="modal fade" id="modal-new-pedido" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-new-pedido">
                            <?php
                                //GERANDO O SERIAL
                        
                                $rnd1 = strtoupper(substr(base64_encode(md5(rand())),0,2));
                                $rnd2 = strtoupper(substr(base64_encode(md5(rand())),0,2));
                                $rnd3 = strtoupper(substr(base64_encode(md5(rand())),0,2));
                                $serial = $rnd1.date('dm').$rnd2.$rnd3;
                            ?>
                            <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">
                            <input type="hidden" name="codigo" value="<?php echo $serial; ?>">

                            <div class="modal-header">
                                <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">
                                    <span class="hidden-xs">Novo or&ccedil;amento&#47;pedido</span>
                                    <span class="hidden-xs pull-right"><strong><?php echo $serial; ?></strong></span>
                                    <span class="hidden-sm hidden-md hidden-lg"><strong><?php echo $serial; ?></strong></span>
                                </h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text text-danger" for="vendedor">Vendedor</label>
                                            <div class="input-group col-md-12">
                                                <select name="vendedor" id="vendedor" class="form-control" title="Vincule o vendedor" placeholder="Vendedor" required>
                                                    <option value="" selected>Vincule um vendedor ao orçamento/pedido</option>
                                                    <?php
                                                        try {
                                                            //buscando os vendedores
                                                            $sql2 = $pdo->prepare("SELECT idvendedor,nome AS vendedor FROM vendedor WHERE monitor = :monitor ORDER BY nome");
                                                            $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                            $sql2->execute();
                                                            $ret2 = $sql2->rowCount();
                            
                                                                if($ret2 > 0) {
                                                                    while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                                        echo'<option value="'.$lin2->idvendedor.'">'.$lin2->vendedor.'</option>';
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
                                            <label class="text text-danger" for="cliente">Cliente <cite class="cite-load-cliente"><i class="fa fa-cog fa-spin"></i></cite></label>
                                            <div class="input-group col-md-12">
                                                <select name="cliente" id="cliente" class="" title="Vincule o cliente" placeholder="Cliente" style="max-width: 100%;width: 100%;" required>
                                                    <option value="" selected>Vincule um cliente ao orçamento/pedido</option>
                                                    <?php
                                                        /*try {
                                                            //buscando os clientes
                                                            $sql2 = $pdo->prepare("SELECT idcliente,nome AS cliente,cidade FROM cliente WHERE monitor = :monitor ORDER BY cidade,nome");
                                                            $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                            $sql2->execute();
                                                            $ret2 = $sql2->rowCount();
                            
                                                                if($ret2 > 0) {
                                                                    while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                                        echo'<option value="'.$lin2->idcliente.'">'.$lin2->cidade.'&#58; '.$lin2->cliente.'</option>';
                                                                    }
                                                                }
                                                            
                                                            unset($sql2,$ret2,$lin2,$monitor);
                                                        }
                                                        catch(PDOException $e) {
                                                            echo'Erro ao conectar o servidor '.$e->getMessage();
                                                        }*/
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="Tipo">Tipo</label>
                                            <div class="input-group col-md-12">
                                                <span class="form-icheck"><input type="radio" name="tipo" value="O" checked> Or&ccedil;amento</span>
                                                <span class="form-icheck"><input type="radio" name="tipo" value="P"> Pedido</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="datado">Data</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="datado" id="datado" class="form-control" value="<?php echo date('d/m/Y'); ?>" maxlength="10" title="Informe o celular do cliente" placeholder="Data" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="hora">Hora</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="hora" id="hora" class="form-control" value="<?php echo date('H:i:s'); ?>" maxlength="8" title="Informe o celular do cliente" placeholder="Hora" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-new-pedido">Avan&ccedil;ar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-pedido" role="dialog" aria-hidden="true">
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
        <script src="js/datepicker.min.js"></script>
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

                /* DATEPICKER */
    
                $("#datado").show(function () {
                    $("#datado").datepicker({
                        format: 'dd/mm/yyyy',
                        todayHighlight: true,
                        autoclose: true
                    });
                });

                $(".date-pick").show(function () {
                    $(".date-pick").datepicker({
                        language: 'pt-BR',
                        format: "mm yyyy",
                        startView: 1,
                        minViewMode: 1
                    }).on('hide', function(e) {
                        var dt = e.target.value.split(' ');
                        location.href = "inicio-adm?<?php echo $getmes; ?>=" + dt[0] + "&<?php echo $getano; ?>=" + dt[1] + "&pick=1";
                    });
                });

                /* MASK */
    
                $("#hora").show(function(){
                    $('#hora').inputmask('99:99:99');
                });

                /* AUTOSELECT */

                $('#vendedor').change(function(){
                    if($(this).val()) {
                        $('#cliente').hide();
                        $('.cite-load-cliente').show();
                        
                        $.getJSON('appLoadCliente.php?',{idvendedor: $(this).val(), ajax: 'true'}, function(j){
                            if(j != 'false') {
                                var options = '<option value="" selected>Vincule um cliente ao orçamento/pedido</option>';	
                            
                                    for(var i = 0; i < j.length; i++){
                                        options += '<option value="' + j[i].idcliente + '">' + j[i].cidade + ': ' + j[i].nome + '</option>';
                                    }
                                
                                $('#cliente').html(options).show();
                                $('.cite-load-cliente').hide();
                            }
                        });
                    } else {
                        $('#cliente').html('<option value="" selected>Vincule um cliente ao orçamento/pedido</option>');
                    }
                });

                /* AUTOCOMPLETE */
                        
                $("#cliente").show(function () {
                    $("#cliente").select2({
                        placeholder: "Vincule um cliente ao orçamento/pedido",
                        allowClear: true
                    });
                });

                /* PRINT BOXES */

                $('.box-option').iCheck({
                    handle: 'checkbox',
                    checkboxClass: 'icheckbox_minimal'
                });

                $('.box-option').on('ifChecked', function(e) {
                    e.preventDefault();
                    $('.btn-box-select-all').removeClass('hide');
                    $('.btn-box-print').removeClass('hide');
                    $('.btn-box-delete').removeClass('hide');
                });

                $('.box-option').on('ifUnchecked', function(e) {
                    e.preventDefault();
                    
                        if($('.box-option:checked').length == 0) {
                            $('.btn-box-select-all').addClass('hide');
                            $('.btn-box-print').addClass('hide');
                            $('.btn-box-delete').addClass('hide');
                        }
                });

                /* Faz os checkboxs ficarem sem o iCheck
                $('.box-print').on('change', function(e) {
                    e.preventDefault();
                    
                    if($('.box-print').is(':checked')) {
                        $('.btn-box-print').removeClass('hide');
                    } else {
                        if($('.box-print:checked').length == 0) {
                            $('.btn-box-print').addClass('hide');
                        }
                    }
                });*/

                $('.btn-box-select-all').click(function (e) {
                    e.preventDefault();
                    $('.box-option').iCheck('check');
                    $('.btn-box-select-all').addClass('hide');
                    $('.btn-box-unselect-all').removeClass('hide');
                });

                $('.btn-box-unselect-all').click(function (e) {
                    e.preventDefault();
                    $('.box-option').iCheck('uncheck');
                    $('.btn-box-unselect-all').addClass('hide');
                    // $('.btn-box-select-all').removeClass('hide');
                });

                $('.btn-box-print').click(function (e) {
                    e.preventDefault();
                    var boxes = [];

                    $("input[name='box_option[]']:checked").each(function(i) {
                        boxes.push($(this).val());
                    });

                    location.href = 'pedidoProdutoPrintBox.php?box_option=' + boxes;
                    //$.get('pedidoProdutoPrintBox.php', {box_print: boxes});
                });

                $('.btn-box-delete').click(function (e) {
                    e.preventDefault();
                    var boxes = [];

                    $("input[name='box_option[]']:checked").each(function(i) {
                        boxes.push($(this).val());
                    });

                    //location.href = 'pedidoProdutoDeleteBox.php?box_option=' + boxes;
                    
                    $.smkConfirm({
                        text: 'Excluir o orçamento/pedido?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if(res) {
                            location.href = 'pedidoProdutoDeleteBox.php?box_option=' + boxes;
                        }
                    });
                });

                /* CRUD */

                //Novo pedido

                $(".form-new-pedido").submit(function(e){
                    e.preventDefault();

                    $.post('pedidoInsert.php', $(this).serialize(), function(data){
                        $(".btn-new-pedido").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        if(data == 'reload'){
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                        }/*else if(data == 'true'){
                            $.smkAlert({text: 'Orçamento/Pedido cadastrado com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='produto-no-pedido'", delay);
                        }*/else if(data.match(/<url>/g)){
                            $.smkAlert({text: 'Orçamento/Pedido cadastrado com sucesso.', type: 'success', time: 2});
                            data = data.replace("<url>","");
                            data = data.replace("</url>","");
                            window.setTimeout("location.href='" + data + "'", delay);
                        }else{
                            $.smkAlert({text: data, type: 'warning', time: 3});
                        }

                        $(".btn-new-pedido").html('Salvar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete pedido
    
                $(".table-data").on('click', '.a-delete-pedido', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        py = click[0],
                        id = click[1];
                    
                    $.smkConfirm({
                        text: 'Excluir o orçamento/pedido?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'pedidoDelete.php?' + py + '=' + id;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg); ?>