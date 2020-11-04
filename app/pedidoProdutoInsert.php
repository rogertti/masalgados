<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
        
    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";

        if(empty($_POST['idpedido'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['produto'])) { die($msg); } else {
            $filtro = 1;
            $produto = explode('-', $_POST['produto']);
        }
        if(empty($_POST['valor_venda'])) { die($msg); } else { $filtro++; }
        if(empty($_POST['quantidade'])) { die($msg); } else { $filtro++; }
        if(empty($_POST['subtotal'])) { die($msg); } else {
            $filtro++;
            $_POST['subtotal'] = str_replace(',','',$_POST['subtotal']);
        }

        if($filtro == 4) {
            try {
                include_once('appConnection.php');

                /* INSERE NO BANCO */

                $sql = $pdo->prepare("INSERT INTO produto_no_pedido (pedido_idpedido,produto_idproduto,quantidade,subtotal,bonus) VALUES (:idpedido,:idproduto,:quantidade,:subtotal,:bonus)");
                $sql->bindParam(':idpedido', $_POST['idpedido'], PDO::PARAM_INT);
                $sql->bindParam(':idproduto', $produto[0], PDO::PARAM_INT);
                $sql->bindParam(':quantidade', $_POST['quantidade'], PDO::PARAM_INT);
                $sql->bindParam(':subtotal', $_POST['subtotal'], PDO::PARAM_STR);
                $sql->bindParam(':bonus', $_POST['bonus'], PDO::PARAM_STR);
                $res = $sql->execute();

                    if(!$res) {
                        var_dump($sql->errorInfo());
                        exit;
                    } else {
                        echo'true';

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' adicionou um produto no orçamento/pedido.';

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

                unset($sql,$res,$idpedido,$pyid,$pycc);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro);
?>