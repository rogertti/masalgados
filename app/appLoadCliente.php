<?php
    /*require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }*/

    /* CLEAR CACHE */
    
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: application/json');

    try {
        include_once('appConnection.php');

        //buscando os clientes
        
        #$idvendedor = $pdo->quote($_REQUEST['idvendedor']);
        #$idvendedor = $_REQUEST['idvendedor']);
        $monitor = 'T';
        $sql = $pdo->prepare("SELECT cliente.idcliente,cliente.nome AS cliente,cliente.cidade FROM cliente INNER JOIN praca ON cliente.praca_idpraca = praca.idpraca INNER JOIN vendedor ON praca.vendedor_idvendedor = vendedor.idvendedor WHERE vendedor.idvendedor = :idvendedor AND cliente.monitor = :monitor ORDER BY cliente.cidade,cliente.nome");
        $sql->bindParam(':idvendedor', $_REQUEST['idvendedor'], PDO::PARAM_INT);
        $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
        $sql->execute();
        $ret = $sql->rowCount();

            if($ret > 0) {
                $cliente = array();

                    while($lin = $sql->fetch(PDO::FETCH_OBJ)) {
                        $cliente[] = array('idcliente' => $lin->idcliente, 'nome' => $lin->cliente, 'cidade' => $lin->cidade);
                    }
                
                echo json_encode($cliente);

                unset($lin,$cliente);
            } else {
                echo'false';
            }

        unset($sql,$ret,$monitor);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }

    unset($pdo,$e);
?>