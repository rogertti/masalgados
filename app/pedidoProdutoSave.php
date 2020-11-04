<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    //Antes de imprimir, atualiza a forma de pagamento e o desconto.

    if(empty($_POST['idpedido'])) { die("Vari&aacute;vel de controle nula."); }
    if(!empty($_POST['forma_pagamento'])) {
        $_POST['forma_pagamento'] = str_replace("'","&#39;",$_POST['forma_pagamento']);
        $_POST['forma_pagamento'] = str_replace('"','&#34;',$_POST['forma_pagamento']);
        $_POST['forma_pagamento'] = str_replace('%','&#37;',$_POST['forma_pagamento']);
    }

        try {
            include_once('appConnection.php');

            //atualizando

            $sql = $pdo->prepare("UPDATE pedido SET desconto = :desconto,forma_pagamento = :forma_pagamento WHERE idpedido = :idpedido");
            $sql->bindParam(':idpedido', $_POST['idpedido'], PDO::PARAM_INT);
            $sql->bindParam(':desconto', $_POST['desconto'], PDO::PARAM_INT);
            $sql->bindParam(':forma_pagamento', $_POST['forma_pagamento'], PDO::PARAM_STR);
            $res = $sql->execute();

                if(!$res) {
                    var_dump($sql->errorInfo());
                    exit;
                } else {
                    $py = md5('idpedido');
                    echo'<url>pedidoProdutoPrint.php?'.$py.'='.$_POST['idpedido'].'</url>';

                    //Registrando LOG

                    $log_datado = date('Y-m-d');
                    $log_hora = date('H:i:s');
                    $log_descricao = 'Usuário '.$_SESSION['seller'].' salvou o desconto e forma de pagamento de um orçamento/pedido.';

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

            unset($sql,$res,$py);
        }
        catch(PDOException $e) {
            echo'Falha ao conectar o servidor '.$e->getMessage();
        }

    unset($pdo,$e);
?>