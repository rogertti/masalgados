<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    try {
        include_once('appConnection.php');

        /* ATUALIZA NO BANCO */

        $sql = $pdo->prepare("TRUNCATE TABLE log");
        $res = $sql->execute();

            if(!$res) {
                var_dump($sql->errorInfo());
                exit;
            } else {
                header('location:log');
            }

        unset($sql,$res);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }
    
    unset($pdo,$e);
?>