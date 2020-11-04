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

        $dia = substr($_GET['search_keyword'],0,2);
        $mes = substr($_GET['search_keyword'],3,2);
        $ano = substr($_GET['search_keyword'],6);
        $monitor = 'T';

            //Se a busca for uma data, aponta apenas para a tabela pedido

            if((is_numeric($dia)) AND (is_numeric($mes)) AND (is_numeric($ano))) {
                if(checkdate($mes, $dia, $ano)) {
                    $_GET['search_keyword'] = $ano.'-'.$mes.'-'.$dia;
                    
                    $sql = $pdo->prepare("SELECT pedido.idpedido,pedido.codigo,pedido.tipo,pedido.datado,pedido.hora,pedido.forma_pagamento,vendedor.nome AS vendedor,cliente.nome AS cliente,praca.nome AS praca FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente INNER JOIN praca ON  cliente.praca_idpraca = praca.idpraca AND praca.vendedor_idvendedor = vendedor.idvendedor WHERE pedido.datado = :datado AND pedido.monitor = :monitor ORDER BY pedido.tipo,pedido.datado,pedido.hora,vendedor.nome,cliente.nome");
                    $sql->bindParam(':datado', $_GET['search_keyword'], PDO::PARAM_STR);
                    $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                    $sql->execute();
                    $ret = $sql->rowCount();

                        if($ret > 0) {
                            $py = md5('idpedido');

                                while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                    switch($lin->tipo) {
                                        case 'O': $lin->tipo = 'OR&Ccedil;AMENTO'; break;
                                        case 'P': $lin->tipo = 'PEDIDO'; break;
                                    }

                                    $ano = substr($lin->datado,0,4);
                                    $mes = substr($lin->datado,5,2);
                                    $dia = substr($lin->datado,8);
                                    $lin->datado = $dia."/".$mes."/".$ano;

                                    switch($lin->forma_pagamento) {
                                        case 'avista': $lin->forma_pagamento = '&Agrave vista'; break;
                                        case '7dias': $lin->forma_pagamento = '7 dias'; break;
                                        case '14dias': $lin->forma_pagamento = '14 dias'; break;
                                        case '21dias': $lin->forma_pagamento = '21 dias'; break;
                                        case '28dias': $lin->forma_pagamento = '28 dias'; break;
                                    }

                                    echo'
                                    <div class="col-md-6 margin-top-10">
                                        <div class="text-left">
                                            <cite class="label label-default">'.$lin->tipo.'</cite>
                                            <h3 class="text-info">
                                                <span>'.$lin->codigo.'</span>
                                                <span class="pull-right hidden-xs">
                                                    <span class="label label-info"><a class="text-white" title="Imprimir o pedido" href="pedidoProdutoPrint.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-print"></i></a></span>    
                                                    <span class="label label-primary"><a class="text-white" title="Editar os produtos do pedido" href="pedidoProduto.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-tag"></i></a></span>
                                                    <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-pedido-search" title="Editar o pedido" href="pedidoEdit.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-pencil"></i></a></span>
                                                    <span class="label label-danger"><a class="text-white a-delete-pedido-search" id="'.$py.'-'.$lin->idpedido.'" title="Excluir o pedido" href="#"><i class="fa fa-trash-o"></i></a></span>
                                                </span>
                                            </h3>
                                            <h4>Vendedor&#58; <strong>'.$lin->vendedor.'</strong></h4>
                                            <h4>Pra&ccedil;a&#58; <strong>'.$lin->praca.'</strong></h4>
                                            <h4>Cliente&#58; <strong>'.$lin->cliente.'</strong></h4>
                                            <h4>Pagamento&#58; <strong>'.$lin->forma_pagamento.'</strong></h4>
                                            <h4>'.$lin->datado.' &#45; '.$lin->hora.' h</h4>
                                            <p class="p-action hidden-sm hidden-md hidden-lg">
                                                <span class="label label-info"><a class="text-white" title="Imprimir o pedido" href="pedidoProdutoPrint.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-print fa-lg"></i></a></span>    
                                                <span class="label label-primary"><a class="text-white" title="Editar os produtos do pedido" href="pedidoProduto.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-tag fa-lg"></i></a></span>
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-pedido-search" title="Editar o pedido" href="pedidoEdit.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-pedido-search" id="'.$py.'-'.$lin->idpedido.'" title="Excluir o pedido" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                            </p>
                                        </div>
                                    </div>
                                    <hr class="hidden-md hidden-lg">';

                                    unset($dia,$mes,$ano);
                                }

                            unset($py,$lin);
                        } else {
                            echo'
                            <div class="margin-top-30">
                                <p class="lead"><strong>Nada encontrado nessa data</strong></p>
                            </div>';
                        }

                    unset($sql,$ret);
                }
            }

            //Se a busca não for uma data, varre todas as tabelas em busca do critério

            else {
                $_GET['search_keyword'] = '%'.$_GET['search_keyword'].'%';
                $search_keyword = str_replace(' ', '', $_GET['search_keyword']);
                $null = 1;

                //Tabela CLIENTE

                $sql = $pdo->prepare("SELECT cliente.idcliente,cliente.nome AS cliente,cliente.cpf_cnpj,cliente.endereco,cliente.bairro,cliente.cidade,cliente.estado,cliente.telefone,cliente.celular,cliente.email,praca.nome AS praca,vendedor.nome AS vendedor FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE (cliente.nome LIKE :keyword) OR (cliente.cpf_cnpj LIKE :keyword) OR (cliente.endereco LIKE :keyword) OR (cliente.bairro LIKE :keyword) OR (cliente.cidade LIKE :keyword) OR (cliente.telefone LIKE :keyword) OR (cliente.celular LIKE :keyword) AND cliente.monitor = :monitor");
                $sql->bindParam(':keyword', $_GET['search_keyword'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py = md5('idcliente');

                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                $documento = strlen($lin->cpf_cnpj);

                                    switch($documento) {
                                        case 18: $documento = 'CNPJ'; break;
                                        case 14: $documento = 'CPF'; break;
                                        default: $documento = 'DOC'; break;
                                    }

                                echo'
                                <div class="col-md-6 margin-top-10">
                                    <div class="text-left">
                                        <cite class="label label-default">CLIENTE</cite>
                                        <h3 class="text-info">
                                            <span>'.$lin->cliente.'</span>
                                            <span class="pull-right hidden-xs">
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-cliente-search" title="Editar os cadastro do cliente" href="clienteEdit.php?'.$py.'='.$lin->idcliente.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-cliente-search" id="'.$py.'-'.$lin->idcliente.'" title="Excluir o cadastro do cliente" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                            </span>
                                        </h3>
                                        <h4>Pra&ccedil;a&#58; <strong>'.$lin->praca.'</strong></h4>
                                        <h4>Vendedor&#58; <strong>'.$lin->vendedor.'</strong></h4>
                                        <h4>'.$documento.'&#58; <strong>'.$lin->cpf_cnpj.'</strong></h4>
                                        <h4><strong>'.$lin->endereco.'</strong></h4>
                                        <h4><strong>'.$lin->bairro.' &#45; '.$lin->cidade.'</strong></h4>
                                        <h4>'.$lin->telefone.' &#45; '.$lin->celular.'</h4>
                                        <h4>'.$lin->email.'</h4>
                                        <p class="p-action hidden-sm hidden-md hidden-lg">
                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-cliente-search" title="Editar os cadastro do cliente" href="clienteEdit.php?'.$py.'='.$lin->idcliente.'"><i class="fa fa-pencil fa-lg"></i></a></span>    
                                            <span class="label label-danger"><a class="text-white a-delete-cliente-search" id="'.$py.'-'.$lin->idcliente.'" title="Excluir o cadastro do cliente" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                        </p>
                                    </div>
                                </div>
                                <hr class="hidden-md hidden-lg">';
                            }
                        
                        unset($lin,$py);
                    } else {
                        $null++;
                    }

                unset($sql,$ret);

                //Tabela PEDIDO

                $sql = $pdo->prepare("SELECT pedido.idpedido,pedido.codigo,pedido.tipo,pedido.datado,pedido.hora,pedido.forma_pagamento,cliente.nome AS cliente,vendedor.nome AS vendedor FROM pedido INNER JOIN vendedor ON pedido.vendedor_idvendedor = vendedor.idvendedor INNER JOIN cliente ON pedido.cliente_idcliente = cliente.idcliente WHERE (pedido.codigo LIKE :keyword) OR (pedido.forma_pagamento LIKE :keyword) AND pedido.monitor = :monitor");
                $sql->bindParam(':keyword', $search_keyword, PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py = md5('idpedido');

                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                switch($lin->tipo) {
                                    case 'O': $lin->tipo = 'OR&Ccedil;AMENTO'; break;
                                    case 'P': $lin->tipo = 'PEDIDO'; break;
                                }

                                $ano = substr($lin->datado,0,4);
                                $mes = substr($lin->datado,5,2);
                                $dia = substr($lin->datado,8);
                                $lin->datado = $dia."/".$mes."/".$ano;

                                switch($lin->forma_pagamento) {
                                    case 'avista': $lin->forma_pagamento = '&Agrave vista'; break;
                                    case '7dias': $lin->forma_pagamento = '7 dias'; break;
                                    case '14dias': $lin->forma_pagamento = '14 dias'; break;
                                    case '21dias': $lin->forma_pagamento = '21 dias'; break;
                                    case '28dias': $lin->forma_pagamento = '28 dias'; break;
                                }

                                echo'
                                <div class="col-md-6 margin-top-10">
                                    <div class="text-left">
                                        <cite class="label label-default">'.$lin->tipo.'</cite>
                                        <h3 class="text-info">
                                            <span>'.$lin->codigo.'</span>
                                            <span class="pull-right hidden-xs">
                                                <span class="label label-info"><a class="text-white" title="Imprimir o pedido" href="pedidoProdutoPrint.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-print"></i></a></span>    
                                                <span class="label label-primary"><a class="text-white" title="Editar os produtos do pedido" href="pedidoProduto.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-tag"></i></a></span>
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-pedido-search" title="Editar o pedido" href="pedidoEdit.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-pencil"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-pedido-search" id="'.$py.'-'.$lin->idpedido.'" title="Excluir o pedido" href="#"><i class="fa fa-trash-o"></i></a></span>
                                            </span>
                                        </h3>
                                        <h4>Vendedor&#58; <strong>'.$lin->vendedor.'</strong></h4>
                                        <h4>Cliente&#58; <strong>'.$lin->cliente.'</strong></h4>
                                        <h4>Pagamento&#58; <strong>'.$lin->forma_pagamento.'</strong></h4>
                                        <h4>'.$lin->datado.' &#45; '.$lin->hora.' h</h4>
                                        <p class="p-action hidden-sm hidden-md hidden-lg">
                                            <span class="label label-info"><a class="text-white" title="Imprimir o pedido" href="pedidoProdutoPrint.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-print fa-lg"></i></a></span>    
                                            <span class="label label-primary"><a class="text-white" title="Editar os produtos do pedido" href="pedidoProduto.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-tag fa-lg"></i></a></span>
                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-pedido-search" title="Editar o pedido" href="pedidoEdit.php?'.$py.'='.$lin->idpedido.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                            <span class="label label-danger"><a class="text-white a-delete-pedido-search" id="'.$py.'-'.$lin->idpedido.'" title="Excluir o pedido" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                        </p>
                                    </div>
                                </div>
                                <hr class="hidden-md hidden-lg">';

                                unset($dia,$mes,$ano);
                            }
                        
                        unset($lin,$py);
                    } else {
                        $null++;
                    }

                unset($sql,$ret);

                //Tabela PRAÇA

                $sql = $pdo->prepare("SELECT praca.idpraca,praca.nome AS praca,vendedor.nome AS vendedor FROM praca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE (praca.nome LIKE :keyword) AND praca.monitor = :monitor");
                $sql->bindParam(':keyword', $_GET['search_keyword'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py = md5('idpraca');

                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                echo'
                                <div class="col-md-6 margin-top-10">
                                    <div class="text-left">
                                        <cite class="label label-default">PRA&Ccedil;A</cite>
                                        <h3 class="text-info">
                                            <span>'.$lin->praca.'</span>
                                            <span class="pull-right hidden-xs">
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-praca-search" title="Editar os cadastro do praca" href="pracaEdit.php?'.$py.'='.$lin->idpraca.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-praca-search" id="'.$py.'-'.$lin->idpraca.'" title="Excluir o cadastro do praca" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                            </span>
                                        </h3>
                                        <h4>Vendedor&#58; <strong>'.$lin->vendedor.'</strong></h4>
                                        <p class="p-action hidden-sm hidden-md hidden-lg">
                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-praca-search" title="Editar os cadastro do praca" href="pracaEdit.php?'.$py.'='.$lin->idpraca.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                            <span class="label label-danger"><a class="text-white a-delete-praca-search" id="'.$py.'-'.$lin->idpraca.'" title="Excluir o cadastro do praca" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                        </p>
                                    </div>
                                </div>
                                <hr class="hidden-md hidden-lg">';
                            }
                        
                        unset($lin,$py);
                    } else {
                        $null++;
                    }

                unset($sql,$ret);

                //Tabela PRODUTO

                $sql = $pdo->prepare("SELECT idproduto,descricao AS produto,valor_custo,valor_venda FROM produto WHERE (descricao LIKE :keyword) AND monitor = :monitor");
                $sql->bindParam(':keyword', $_GET['search_keyword'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py = md5('idproduto');

                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                echo'
                                <div class="col-md-6 margin-top-10">
                                    <div class="text-left">
                                    <cite class="label label-default">PRODUTO</cite>
                                        <h3 class="text-info">
                                            <span>'.$lin->produto.'</span>
                                            <span class="pull-right hidden-xs">
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-produto-search" title="Editar o produto" href="produtoEdit.php?'.$py.'='.$lin->idproduto.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-produto-search" id="'.$py.'-'.$lin->idproduto.'" title="Excluir o produto" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                            </span>
                                        </h3>
                                        <h4>Valor de venda&#58; <strong>R$ '.number_format($lin->valor_venda,2,'.',',').'</strong></h4>
                                        <p class="p-action hidden-sm hidden-md hidden-lg">
                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-produto-search" title="Editar o produto" href="produtoEdit.php?'.$py.'='.$lin->idproduto.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                            <span class="label label-danger"><a class="text-white a-delete-produto-search" id="'.$py.'-'.$lin->idproduto.'" title="Excluir o produto" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>
                                        </p>
                                    </div>
                                </div>
                                <hr class="hidden-md hidden-lg">';
                            }
                        
                        unset($lin,$py);
                    } else {
                        $null++;
                    }

                unset($sql,$ret);

                //Tabela VENDEDOR

                $sql = $pdo->prepare("SELECT idvendedor,nome AS vendedor,email FROM vendedor WHERE (nome LIKE :keyword) AND monitor = :monitor");
                $sql->bindParam(':keyword', $_GET['search_keyword'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $py = md5('idvendedor');

                            while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                                echo'
                                <div class="col-md-6 margin-top-10">
                                    <div class="text-left">
                                    <cite class="label label-default">VENDEDOR</cite>
                                        <h3 class="text-info">
                                            <span>'.$lin->vendedor.'</span>
                                            <span class="pull-right hidden-xs">
                                                <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-vendedor-search" title="Editar os dados do vendedor" href="vendedorEdit.php?'.$py.'='.$lin->idvendedor.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                                <span class="label label-danger"><a class="text-white a-delete-vendedor-search" id="'.$py.'-'.$lin->idvendedor.'" title="Excluir o cadastro do vendedor" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>                                            
                                            </span>
                                        </h3>
                                        <h4><strong>'.$lin->email.'</strong></h4>
                                        <p class="p-action hidden-sm hidden-md hidden-lg">
                                            <span class="label label-warning"><a class="text-white" data-toggle="modal" data-target="#modal-edit-vendedor-search" title="Editar os dados do vendedor" href="vendedorEdit.php?'.$py.'='.$lin->idvendedor.'"><i class="fa fa-pencil fa-lg"></i></a></span>
                                            <span class="label label-danger"><a class="text-white a-delete-vendedor-search" id="'.$py.'-'.$lin->idvendedor.'" title="Excluir o cadastro do vendedor" href="#"><i class="fa fa-trash-o fa-lg"></i></a></span>                                            
                                        </p>
                                    </div>
                                </div>
                                <hr class="hidden-md hidden-lg">';
                            }
                        
                        unset($lin,$py);
                    } else {
                        $null++;
                    }

                    if($null == 6) {
                        echo'
                        <div class="margin-top-30">
                            <p class="lead"><strong>Nada encontrado</strong></p>
                        </div>';
                    }

                unset($sql,$ret,$null);
            }

        //Registrando LOG

        $log_datado = date('Y-m-d');
        $log_hora = date('H:i:s');
        $log_descricao = 'Usuário '.$_SESSION['seller'].' buscou por '.$_GET['search_keyword'].'.';

        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
        $res_log = $sql_log->execute();

            if(!$res_log) {
                var_dump($sql_log->errorInfo());
            }

        unset($dia,$mes,$ano,$monitor,$search_keyword,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }

    unset($pdo,$e);
?>