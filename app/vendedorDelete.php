<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    try {
        include_once('appConnection.php');
        
        /* ATUALIZA NO BANCO */

        $py = md5('idvendedor');
        $monitor = 'F';
        $sql = $pdo->prepare("UPDATE vendedor SET monitor = :monitor WHERE idvendedor = :idvendedor");
        $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
        $sql->bindParam(':idvendedor', $_GET[''.$py.''], PDO::PARAM_INT);
        $res = $sql->execute();

            if(!$res) {
                var_dump($sql->errorInfo());
                exit;
            } else {
                header('location:vendedor');

                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' apagou um vendedor.';

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

        unset($sql,$res,$py,$monitor);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }
    
    unset($pdo,$e);
?>