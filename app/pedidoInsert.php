<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";

        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['codigo'])) { die($msg); } else { $filtro = 1; }
        if(empty($_POST['vendedor'])) { die($msg); } else { $filtro++; }
        if(empty($_POST['cliente'])) { die($msg); } else { $filtro++; }
        if(empty($_POST['tipo'])) { die($msg); } else { $filtro++; }
        if(empty($_POST['datado'])) { die($msg); } else {
            $filtro++;
        
            $dia = substr($_POST['datado'],0,2);
            $mes = substr($_POST['datado'],3,2);
            $ano = substr($_POST['datado'],6);
            $_POST['datado'] = $ano."-".$mes."-".$dia;
            unset($dia,$mes,$ano);
        }
        if(empty($_POST['hora'])) { die($msg); } else { $filtro++; }

        if($filtro == 6) {
            try {
                include_once('appConnection.php');
                
                /* CONTROLE DE DUPLICATAS */

                $sql = $pdo->prepare("SELECT idpedido,monitor FROM pedido WHERE codigo = :codigo");
                $sql->bindParam(':codigo', $_POST['codigo'], PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $py = md5('idpedido');
                        
                            if($lin->monitor == 'T') {
                                die('Esse or&ccedil;amento/pedido j&aacute; est&aacute; cadastrado.');
                            }
                            
                            if($lin->monitor == 'F') {
                                die('Esse or&ccedil;amento/pedido j&aacute; est&aacute; cadastrado, mas est&aacute; desativado. <a href="pedidoActivate.php?'.$py.'='.$lin->idpedido.'" title="Ativar o or&ccedil;amento/pedido">Clique para ativar.</a>');    
                            }
                    }

                unset($sql,$ret,$lin,$py);

                /* INSERE NO BANCO */

                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO pedido (vendedor_idvendedor,cliente_idcliente,tipo,codigo,datado,hora,monitor) VALUES (:vendedor,:cliente,:tipo,:codigo,:datado,:hora,:monitor)");
                $sql->bindParam(':vendedor', $_POST['vendedor'], PDO::PARAM_INT);
                $sql->bindParam(':cliente', $_POST['cliente'], PDO::PARAM_INT);
                $sql->bindParam(':tipo', $_POST['tipo'], PDO::PARAM_STR);
                $sql->bindParam(':codigo', $_POST['codigo'], PDO::PARAM_STR);
                $sql->bindParam(':datado', $_POST['datado'], PDO::PARAM_STR);
                $sql->bindParam(':hora', $_POST['hora'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $res = $sql->execute();

                    if(!$res) {
                        var_dump($sql->errorInfo());
                        exit;
                    } else {
                        $idpedido = $pdo->lastInsertId();
                        $py = md5('idpedido');
                        echo'<url>pedidoProduto.php?'.$py.'='.$idpedido.'</url>';

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' cadastrou um orçamento/pedido.';

                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                        $res_log = $sql_log->execute();

                            if(!$res_log) {
                                var_dump($sql_log->errorInfo());
                            }

                        unset($sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                    }

                unset($sql,$res,$monitor,$idpedido,$py);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro);
?>