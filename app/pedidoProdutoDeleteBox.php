<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    include_once('appConnection.php');

    $box_option = explode(',', $_GET['box_option']);
    $py = md5('idpedido');
    $monitor = 'F';

        foreach($box_option as $idpedido) {
            try {
                // Recuperando o código do pedido antes de excluir

                $sql = $pdo->prepare("SELECT codigo FROM pedido WHERE idpedido = :idpedido");
                $sql->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
                $sql->execute();
                $ret = $sql->rowCount();
                
                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $codigo = $lin->codigo;
                    }

                unset($sql,$ret,$lin);

                // Deletando

                $sql = $pdo->prepare("UPDATE pedido SET monitor = :monitor WHERE idpedido = :idpedido");
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $sql->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
                $res = $sql->execute();

                    if(!$res) {
                        var_dump($sql->errorInfo());
                        exit;
                    } else {
                        if($_SESSION['key'] != 'U') {
                            header('location:inicio-adm');
                        } else {
                            header('location:inicio');
                        }
                    }

                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' excluiu o orçamento/pedido '.$codigo.'.';

                $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                $res_log = $sql_log->execute();

                    if(!$res_log) {
                        var_dump($sql_log->errorInfo());
                    }

                unset($sql,$res,$codigo,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
            }
            catch(PDOException $e) {
                echo'Erro ao conectar o servidor '.$e->getMessage();
            }
        } //foreach
        
    unset($pdo,$e,$cfg,$box_option,$py,$idpedido,$monitor);
?>