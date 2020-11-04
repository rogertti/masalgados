<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
    
    $m = 1;
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
                        <span class="hidden-xs">Or&ccedil;amento&#47;Pedido</span>
                        <span class="hidden-xs pull-right lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-add-produto" title="Clique para adicionar um produto ao or&ccedil;amento&#47;pedido" href="#"><i class="fa fa-tag"></i> Add produto</a></span>
                        <span class="hidden-sm hidden-md hidden-lg lead"><a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-add-produto" title="Clique para adicionar um produto ao or&ccedil;amento&#47;pedido" href="#"><i class="fa fa-tag"></i> Adicionar produto</a></span>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                        <?php
                            try {
                                include_once('appConnection.php');
                                
                                //buscando os dados do orçamento/pedido

                                $py = md5('idpedido');
                                $sql = $pdo->prepare("SELECT pedido.idpedido,pedido.tipo,pedido.codigo,pedido.datado,pedido.hora,pedido.desconto,pedido.forma_pagamento,vendedor.nome AS vendedor,cliente.nome AS cliente,cliente.cpf_cnpj,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular,cliente.email FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente WHERE pedido.idpedido = :idpedido");
                                $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    //tratando as rotas
                                    
                                    switch($_SESSION['key']) {
                                        case 'A':
                                            $btn_close = '<a class="btn btn-default btn-flat" href="inicio-adm">Fechar</a>';
                                            $btn_skip = '<a class="btn btn-default btn-flat" href="inicio-adm" title="Pular">Pular</a>';
                                            break;
                                        case 'R':
                                            $btn_close = '<a class="btn btn-default btn-flat" href="inicio-adm">Fechar</a>';
                                            $btn_skip = '<a class="btn btn-default btn-flat" href="inicio-adm" title="Pular">Pular</a>';
                                            break;
                                        case 'U':
                                            $btn_close = '<a class="btn btn-default btn-flat" href="inicio">Fechar</a>';
                                            $btn_skip = '<a class="btn btn-default btn-flat" href="inicio" title="Pular">Pular</a>';
                                            break;
                                    }

                                    if($ret > 0) {
                                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                                        $tipo = $lin->tipo;
                                        $codigo = $lin->codigo;
                                        $desconto = $lin->desconto;
                                        $forma_pagamento = $lin->forma_pagamento;

                                        //invertendo a data
                                        $ano = substr($lin->datado,0,4);
                                        $mes = substr($lin->datado,5,2);
                                        $dia = substr($lin->datado,8);
                                        $lin->datado = $dia."/".$mes."/".$ano;

                                        //verificando CPF ou CNPJ
                                        if(strlen($lin->cpf_cnpj) == 14){
                                            $doc = '<div class="col-md-3 invoice-col">CPF: '.$lin->cpf_cnpj.'</div>';
                                        } else if(strlen($lin->cpf_cnpj) == 18){
                                            $doc = '<div class="col-md-3 invoice-col">CNPJ: '.$lin->cpf_cnpj.'</div>';
                                        }

                                        echo'
                                        <section class="invoice">
                                            <div class="row invoice-info">
                                                <div class="col-md-3 invoice-col"><strong>C&oacute;digo: '.$lin->codigo.'</strong></div>
                                                <div class="col-md-3 invoice-col">Data: '.$lin->datado.'</div>
                                                <div class="col-md-3 invoice-col">Hora: '.$lin->hora.' h</div>
                                                <div class="col-md-3 invoice-col"><strong>Vendedor: '.$lin->vendedor.'</strong></div>
                                            </div>
                                            <hr>
                                            <div class="row invoice-info">
                                                <div class="col-md-6 invoice-col">Cliente: '.$lin->cliente.'</div>
                                                '.$doc.'
                                                <div class="col-md-3 invoice-col">Email: '.$lin->email.'</div>
                                            </div>
                                            <div class="row invoice-info">
                                                <div class="col-md-9 invoice-col">Endere&ccedil;o: '.$lin->endereco.' &#45; '.$lin->bairro.' &#45; '.$lin->cidade.' &#45; '.$lin->estado.'</div>
                                                <div class="col-md-3 invoice-col">Contato: '.$lin->telefone.' &#45; '.$lin->celular.'</div>
                                            </div>
                                        </section>';

                                        unset($ano,$mes,$dia,$lin,$doc);
                                    } else {
                                        $desconto = '';
                                        $forma_pagamento = '';
                                    }
                                
                                unset($sql,$ret);

                                //buscando os produtos do orçamento/pedido
                                
                                $sql = $pdo->prepare("SELECT pedido.idpedido,produto.idproduto,produto.descricao,produto.valor_venda,produto_no_pedido.quantidade,produto_no_pedido.subtotal,produto_no_pedido.bonus FROM produto_no_pedido INNER JOIN pedido ON produto_no_pedido.pedido_idpedido = pedido.idpedido INNER JOIN produto ON produto_no_pedido.produto_idproduto = produto.idproduto WHERE pedido.idpedido = :idpedido");
                                $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
                                $sql->execute();
                                $ret = $sql->rowCount();

                                    if($ret > 0) {
                                        $py2 = md5('idproduto');
                                        $total = 0;

                                        echo'
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover table-nodata dt-responsive nowrap">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30px;"></th>
                                                        <th>Descri&ccedil;&atilde;o</th>
                                                        <th>Valor de venda</th>
                                                        <th>Quantidade</th>
                                                        <th style="width: 300px;">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                        
                                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                                if($lin->bonus > 0) {
                                                    $lin->bonus = '<span class="pull-right label label-warning">'.$lin->bonus.' de b&ocirc;nus</span>';
                                                }
                                                
                                                echo'
                                                    <tr>
                                                        <td class="td-action">
                                                            <span class="label label-danger"><a class="text-white a-delete-produto" id="'.$py.'-'.$lin->idpedido.'-'.$py2.'-'.$lin->idproduto.'" title="Excluir o produto do orçamento/pedido" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                                        </td>
                                                        <td><span>'.$lin->descricao.'</span>'.$lin->bonus.'</td>
                                                        <td>R$ '.number_format($lin->valor_venda,2,'.',',').'</td>
                                                        <td>'.$lin->quantidade.'</td>
                                                        <td>R$ '.number_format($lin->subtotal,2,'.',',').'</td>
                                                    </tr>';

                                                $total = $total + $lin->subtotal;
                                            }
                                        
                                        //Calculando o desconto, se houver
                                        if(!empty($desconto)) {
                                            $a = $total * $desconto;
                                            $a = number_format($a,2,'.','');
                                            $b = $a / 100;
                                            $total_desconto = $total - $b;
                                            $total_desconto = 'R$ '.number_format($total_desconto,2,'.','');
                                        } else {
                                            $total_desconto = '';
                                        }

                                        echo'
                                                </tbody>
                                                <tfoot>
                                                    <form class="form-print-pedido">
                                                        <input type="hidden" name="idpedido" value="'.$_GET[''.$py.''].'">
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="input-group col-md-6">
                                                                    <!--<input type="text" name="forma_pagamento" class="form-control" value="'.$forma_pagamento.'" maxlength="100" placeholder="Forma de pagamento">-->
                                                                    <select name="forma_pagamento" class="form-control" placeholder="Forma de pagamento">';
                                                                        if($tipo == 'P') {
                                                                            echo'<option value="">Forma de pagamento</option>';
                                                                            if($forma_pagamento == 'avista') { echo'<option value="avista" selected>&Agrave vista</option>'; } else { echo'<option value="avista">&Agrave vista</option>'; }
                                                                            if($forma_pagamento == '7dias') { echo'<option value="7dias" selected>7 dias</option>'; } else { echo'<option value="7dias">7 dias</option>'; }
                                                                            if($forma_pagamento == '14dias') { echo'<option value="14dias" selected>14 dias</option>'; } else { echo'<option value="14dias">14 dias</option>'; }
                                                                            if($forma_pagamento == '21dias') { echo'<option value="21dias" dias>21 dias</option>'; } else { echo'<option value="21dias">21 dias</option>'; }
                                                                            if($forma_pagamento == '28dias') { echo'<option value="28dias" selected>28 dias</option>'; } else { echo'<option value="28dias">28 dias</option>'; }
                                                                        } elseif ($tipo == 'O') {
                                                                            echo'<option value="">Forma de pagamento</option>';
                                                                            if($forma_pagamento == 'avista') { echo'<option value="avista" selected>&Agrave vista</option>'; } else { echo'<option value="avista">&Agrave vista</option>'; }
                                                                        }
                                                                    echo'
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <th>Total: R$ '.number_format($total,2,'.',',').' <input type="hidden" name="total" id="total" value="'.number_format($total,2,'.',',').'"></th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="input-group col-md-2">
                                                                    <input type="number" name="desconto" id="desconto" class="form-control" value="'.$desconto.'" maxlength="5" placeholder="Desconto %">
                                                                </div>
                                                            </td>
                                                            <th>Total com desconto: <span class="total_desconto">'.$total_desconto.'</span> <input type="hidden" name="total_desconto" id="total_desconto"></th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5">
                                                                <button type="submit" class="btn btn-primary btn-flat btn-print-pedido">Salvar &#47; Imprimir</button>
                                                                '.$btn_close.'
                                                            </td>
                                                        </tr>
                                                    </form>
                                                </tfoot>
                                            </table>
                                        </div>
                                        
                                        <div class="div-total-float hidden-sm hidden-md hidden-lg">
                                            <span class="total_pedido">Total: <cite>R$ '.number_format($total,2,'.',',').'</cite></span>
                                            <span>Total com desconto: <cite class="total_desconto">'.$total_desconto.'</cite></span>
                                        </div>';
                                        
                                        //Registrando LOG

                                        $log_datado = date('Y-m-d');
                                        $log_hora = date('H:i:s');
                                        $log_descricao = 'Usuário '.$_SESSION['seller'].' abriu o orçamento/pedido '.$codigo.' para editar os produtos.';

                                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                                        $res_log = $sql_log->execute();

                                            if(!$res_log) {
                                                var_dump($sql_log->errorInfo());
                                            }

                                        unset($py2,$lin,$total,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                                    } else {
                                        echo'
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="lead text-center">
                                                    Nenhum produto adicionado.<br>
                                                    <a class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal-add-produto" title="Clique para adicionar um produto ao or&ccedil;amento&#47;pedido" href="#">
                                                        Adicionar produto
                                                    </a><br>
                                                    '.$btn_skip.'
                                                </p>
                                            </div>
                                        </div>';
                                    }

                                unset($sql,$ret,$btn_close,$btn_skip);
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
            
            <div class="modal fade" id="modal-add-produto" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-add-produto">
                            <input type="hidden" name="idpedido" value="<?php echo $_GET[''.$py.'']; ?>">
                            <input type="hidden" name="rand" value="<?php echo md5(mt_rand()); ?>">

                            <div class="modal-header">
                                <button type="button" class="close closed" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Adicionar produto ao or&ccedil;amento&#47;pedido <span class="pull-right"><strong><?php echo $codigo; ?></strong></span></h4>
                            </div><!-- /.modal-header -->
                            <div class="modal-body overing">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text text-danger" for="produto">Produto</label>
                                            <div class="input-group col-md-12">
                                                <select name="produto" id="produto" class="form-control" title="Selecione o produto" placeholder="Produto" style="max-width: 100%;width: 100%;" required>
                                                    <option value="" selected>Selecione o produto</option>
                                                    <?php
                                                        try {
                                                            //buscando os produtos
                                                            $monitor = 'T';
                                                            $sql2 = $pdo->prepare("SELECT idproduto,descricao,valor_venda FROM produto WHERE monitor = :monitor ORDER BY descricao");
                                                            $sql2->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                                                            $sql2->execute();
                                                            $ret2 = $sql2->rowCount();
                            
                                                                if($ret2 > 0) {
                                                                    while($lin2 = $sql2->fetch(PDO::FETCH_OBJ)) {
                                                                        echo'<option value="'.$lin2->idproduto.'-'.$lin2->valor_venda.'">'.$lin2->descricao.'</option>';
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
                                            <label class="text" for="valor_venda">Valor de venda</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="valor_venda" id="valor_venda" class="form-control" maxlength="18" title="Valor de venda do produto" placeholder="Valor de venda" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text text-danger" for="quantidade">Quantidade</label>
                                            <div class="input-group col-md-4">
                                                <input type="number" name="quantidade" id="quantidade" class="form-control" maxlength="3" title="Informe a quantidade necess&aacute;ria do produto" placeholder="Quantidade" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="bonus">B&ocirc;nus</label>
                                            <div class="input-group col-md-4">
                                                <input type="number" name="bonus" id="bonus" class="form-control" maxlength="3" title="Quantos desse produto s&atilde;o b&ocirc;nus" placeholder="B&ocirc;nus">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text" for="subtotal">Subtotal</label>
                                            <div class="input-group col-md-4">
                                                <input type="text" name="subtotal" id="subtotal" class="form-control" maxlength="18" title="Subtotal do or&ccedil;amento&#47;pedido" placeholder="Subtotal" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-flat pull-left closed" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary btn-flat btn-add-produto">Adicionar</button>
                            </div>
                        </form>
                    </div>
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

                /* AUTOCOMPLETE */
                
                $("#produto").show(function () {
                    $("#produto").select2({
                        placeholder: "Selecione o produto",
                        allowClear: true
                    });
                });

                /* CALC */

                $("#produto").on('change', function() {
                    var opt = this.value.split('-');
                    $("#valor_venda").val(opt[1]);
                });                

                $("#quantidade").keyup(function () {
                    var a = $("#valor_venda").val().replace(',',''),
                        b = $("#quantidade").val(),
                        c = a * b;

                    c = parseFloat(c).toFixed(2);
                    $("#subtotal").val(c);
                });

                $("#bonus").keyup(function () {
                    var a = $("#valor_venda").val().replace(',',''),
                        b = $("#bonus").val(),
                        c = a * b,
                        d = $("#subtotal").val() - c;

                    d = parseFloat(d).toFixed(2);
                    $("#subtotal").val(d);
                });

                $("#desconto").keyup(function () {
                    var a = $("#total").val().replace(',',''),
                        b = $('#desconto').val(),
                        c = a * b,
                        d = c / 100,
                        e = a - d;
                    
                    e = parseFloat(e).toFixed(2);
                    $(".total_desconto").html('R$ ' + e);
                    $("#total_desconto").val(e);
                });

                /* CRUD */

                //Adicionar produto no pedido

                $(".form-add-produto").submit(function(e){
                    e.preventDefault();

                    $.post('pedidoProdutoInsert.php', $(this).serialize(), function(data){
                        $(".btn-add-produto").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        switch (data) {
                        case 'reload':
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                            break;

                        case 'true':
                            $.smkAlert({text: 'Produto adicionado com sucesso.', type: 'success', time: 2});
                            //window.setTimeout("location.href='produto-no-pedido'", delay);
                            location.reload();
                            break;

                        default:
                            $.smkAlert({text: data, type: 'warning', time: 3});
                            break;
                        }

                        $(".btn-add-produto").html('Adicionar').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Imprimir pedido

                $(".form-print-pedido").submit(function(e){
                    e.preventDefault();

                    $.post('pedidoProdutoSave.php', $(this).serialize(), function(data){
                        $(".btn-print-pedido").html('<img src="img/rings.svg" class="loader-svg">').fadeTo(fade, 1);

                        if(data == 'reload'){
                            $.smkAlert({text: 'Nem todos os plugins foram carregados, recarregando...', type: 'danger', time: 2});
                            location.reload();
                        }/*else if(data == 'true'){
                            $.smkAlert({text: 'Orçamento/Pedido cadastrado com sucesso.', type: 'success', time: 2});
                            window.setTimeout("location.href='produto-no-pedido'", delay);
                        }*/else if(data.match(/<url>/g)){
                            $.smkAlert({text: '...', type: 'success', time: 2});
                            data = data.replace("<url>","");
                            data = data.replace("</url>","");
                            window.setTimeout("location.href='" + data + "'", delay);
                        }else{
                            $.smkAlert({text: data, type: 'warning', time: 3});
                        }

                        $(".btn-print-pedido").html('Imprimir').fadeTo(fade, 1);
                    });

                    return false;
                });

                //Delete produto do pedido
    
                $(".table-nodata").on('click', '.a-delete-produto', function (e) {
                    e.preventDefault();
                    
                    var click = this.id.split('-'),
                        pypedido = click[0],
                        idpedido = click[1],
                        pyproduto = click[2],
                        idproduto = click[3];
                    
                    $.smkConfirm({
                        text: 'Excluir o produto do orçamento/pedido?',
                        accept: 'Sim',
                        cancel: 'Não'
                    }, function (res) {
                        if (res) {
                            location.href = 'pedidoProdutoDelete.php?' + pypedido + '=' + idpedido + '&' + pyproduto + '=' + idproduto;
                        }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
<?php unset($m,$pdo,$e,$cfg,$py,$tipo,$codigo,$desconto,$forma_pagamento); ?>