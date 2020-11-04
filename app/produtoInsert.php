<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";

        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['descricao'])) { die($msg); } else {
            $filtro = 1;

            $_POST['descricao'] = str_replace("'","&#39;",$_POST['descricao']);
            $_POST['descricao'] = str_replace('"','&#34;',$_POST['descricao']);
            $_POST['descricao'] = str_replace('%','&#37;',$_POST['descricao']);
        }
        if(empty($_POST['valor_venda'])) { die($msg); } else {
            $filtro++;
            $_POST['valor_venda'] = str_replace(',','',$_POST['valor_venda']);
        }

        if($filtro == 2) {
            try {
                include_once('appConnection.php');
                
                /* CONTROLE DE DUPLICATAS */

                $sql = $pdo->prepare("SELECT idproduto,monitor FROM produto WHERE descricao = :descricao");
                $sql->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $py = md5('idproduto');
                        
                            if($lin->monitor == 'T') {
                                die('Esse produto j&aacute; est&aacute; cadastrado.');
                            }
                            
                            if($lin->monitor == 'F') {
                                die('Esse produto j&aacute; est&aacute; cadastrado, mas est&aacute; desativado. <a href="produtoActivate.php?'.$py.'='.$lin->idproduto.'" title="Ativar o produto">Clique para ativar.</a>');    
                            }
                    }

                unset($sql,$ret,$lin,$py);

                /* INSERE NO BANCO */

                $valor_custo = '0.00';
                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO produto (descricao,valor_custo,valor_venda,monitor) VALUES (:descricao,:valor_custo,:valor_venda,:monitor)");
                $sql->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);
                $sql->bindParam(':valor_custo', $valor_custo, PDO::PARAM_STR);
                $sql->bindParam(':valor_venda', $_POST['valor_venda'], PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $res = $sql->execute();

                    if(!$res) {
                        var_dump($sql->errorInfo());
                        exit;
                    }
                    else {
                        echo'true';

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' cadastrou um produto.';

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

                unset($sql,$res,$valor_custo,$monitor);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro);
?>