<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    try {
        include_once('appConnection.php');
        
        /* ATUALIZA NO BANCO */

        $py = md5('idpedido');
        $py2 = md5('idproduto');
        
        $sql = $pdo->prepare("DELETE FROM produto_no_pedido WHERE pedido_idpedido = :idpedido AND produto_idproduto = :idproduto");
        $sql->bindParam(':idpedido', $_GET[''.$py.''], PDO::PARAM_INT);
        $sql->bindParam(':idproduto', $_GET[''.$py2.''], PDO::PARAM_INT);
        $res = $sql->execute();

            if(!$res) {
                var_dump($sql->errorInfo());
                exit;
            } else {
                header('location:pedidoProduto.php?'.$py.'='.$_GET[''.$py.''].'');

                //Registrando LOG

                $log_datado = date('Y-m-d');
                $log_hora = date('H:i:s');
                $log_descricao = 'Usuário '.$_SESSION['seller'].' um produto do orçamento/pedido.';

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

        unset($sql,$res,$py,$py2);
    }
    catch(PDOException $e) {
        echo'Falha ao conectar o servidor '.$e->getMessage();
    }
    
    unset($pdo,$e);
?>