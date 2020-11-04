<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    /* ENCRYPT */

    function encrypt($data, $k) {
        $l = strlen($k);
        
            if ($l < 16)
                $k = str_repeat($k, ceil(16/$l));

            if ($m = strlen($data)%8)
                $data .= str_repeat("\x00",  8 - $m);
                $val = openssl_encrypt($data, 'AES-256-OFB', $k, 0, $k);
                $val = base64_encode($val);

        return $val;
    }

    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";
    $keys = base64_encode('cripta');

        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['tipo'])) { die($msg); } else { $filtro = 1; }
        if(empty($_POST['nome'])) { die($msg); } else {
            $filtro++;

            $_POST['nome'] = str_replace("'","&#39;",$_POST['nome']);
            $_POST['nome'] = str_replace('"','&#34;',$_POST['nome']);
            $_POST['nome'] = str_replace('%','&#37;',$_POST['nome']);
        }
        if(empty($_POST['usuario'])) { die($msg); } else {
            $filtro++;
            $usuario = base64_decode($_POST['usuario']);
            $usuario = encrypt($_POST['usuario'], $keys);
        }
        if(empty($_POST['senha'])) { die($msg); } else {
            $filtro++;
            $senha = base64_decode($_POST['senha']);
            $senha = encrypt($_POST['senha'], $keys);
        }
        if(empty($_POST['email'])) { die($msg); } else { $filtro++; }

        if($filtro == 5) {
            try {
                include_once('appConnection.php');
                
                /* CONTROLE DE DUPLICATAS */

                $sql = $pdo->prepare("SELECT idvendedor,monitor FROM vendedor WHERE email = :email");
                $sql->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $py = md5('idvendedor');
                        
                            if($lin->monitor == 'T') {
                                die('Esse vendedor j&aacute; est&aacute; cadastrado.');
                            }
                            
                            if($lin->monitor == 'F') {
                                die('Esse vendedor j&aacute; est&aacute; cadastrado, mas est&aacute; desativado. <a href="vendedorActivate.php?'.$py.'='.$lin->idvendedor.'" title="Ativar o cadastro do vendedor">Clique para ativar.</a>');    
                            }
                    }

                unset($sql,$ret,$lin,$py);

                /* INSERE NO BANCO */

                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO vendedor (tipo,nome,usuario,senha,email,monitor) VALUES (:tipo,:nome,:usuario,:senha,:email,:monitor)");
                $sql->bindParam(':tipo', $_POST['tipo'], PDO::PARAM_STR);
                $sql->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                $sql->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $sql->bindParam(':senha', $senha, PDO::PARAM_STR);
                $sql->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
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
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' cadastrou um vendedor.';

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

                unset($sql,$res,$usuario,$senha,$monitor);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro,$keys);
?>