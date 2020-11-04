<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";

        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['vendedor'])) { die($msg); } else { $filtro = 1; }
        if(empty($_POST['nome'])) { die($msg); } else {
            $filtro++;

            $_POST['nome'] = str_replace("'","&#39;",$_POST['nome']);
            $_POST['nome'] = str_replace('"','&#34;',$_POST['nome']);
            $_POST['nome'] = str_replace('%','&#37;',$_POST['nome']);
        }

        if($filtro == 2) {
            try {
                include_once('appConnection.php');
                
                /* CONTROLE DE DUPLICATAS */

                $sql = $pdo->prepare("SELECT idpraca,monitor FROM praca WHERE vendedor_idvendedor = :idvendedor AND nome = :nome");
                $sql->bindParam(':idvendedor', $_POST['vendedor'], PDO::PARAM_INT);
                $sql->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $py = md5('idpraca');
                        
                            if($lin->monitor == 'T') {
                                die('Essa pra&ccedil;a j&aacute; est&aacute; cadastrada.');
                            }
                            
                            if($lin->monitor == 'F') {
                                die('Essa pra&ccedil;a j&aacute; est&aacute; cadastrada, mas est&aacute; desativada. <a href="pracaActivate.php?'.$py.'='.$lin->idpraca.'" title="Ativar a pra&ccedil;a">Clique para ativar.</a>');    
                            }
                    }

                unset($sql,$ret,$lin,$py);

                /* INSERE NO BANCO */

                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO praca (vendedor_idvendedor,nome,monitor) VALUES (:idvendedor,:nome,:monitor)");
                $sql->bindParam(':idvendedor', $_POST['vendedor'], PDO::PARAM_INT);
                $sql->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
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
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' cadastrou uma praça.';

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

                unset($sql,$res,$monitor);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro);
?>