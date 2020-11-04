<?php
    require_once('appConfig.php');

    //encrypt by openssl
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

    //controle de variável
    $msg = "Campo obrigat&oacute;rio vazio.";
    $lock = base64_encode('cripta');

        //depurando os campos
        if(empty($_POST['rand'])) { die('Vari&aacute;vel de controle nula.'); }
        if(empty($_POST['usuario'])) { die($msg); } else {
            $filtro = 1;
            $usuario = base64_decode($_POST['usuario']);
            $usuario = encrypt($_POST['usuario'], $lock);
        }
        if(empty($_POST['senha'])) { die($msg); } else {
            $filtro++;
            $senha = base64_decode($_POST['senha']);
            $senha = encrypt($_POST['senha'], $lock);
        }

        if($filtro == 2) {
            try {
                include_once('appConnection.php');

                //validando o login na tabela login
                $sql = $pdo->prepare("SELECT idvendedor,nome,tipo,monitor FROM vendedor WHERE usuario = :usuario AND senha = :senha");
                $sql->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $sql->bindParam(':senha', $senha, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        
                            if($lin->monitor == 'F') {
                                die('Usu&aacute;rio desativado no sistema.');
                            } else {
                                $_SESSION['id'] = $lin->idvendedor;
                                $_SESSION['seller'] = $lin->nome;
                                $_SESSION['key'] = $lin->tipo;
                                
                                switch($lin->tipo) {
                                    case 'A': echo'admin'; break;
                                    case 'R': echo'root'; break;
                                    case 'U': echo'user'; break;
                                }
                            }

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' entrou no sistema.';

                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                        $res_log = $sql_log->execute();

                            if(!$res_log) {
                                var_dump($sql_log->errorInfo());
                            }

                        unset($lin,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                    } else {
                        //verifica se a tabela está vazia
                        $sql2 = $pdo->prepare("SELECT idvendedor,monitor FROM vendedor");
                        $sql2->execute();
                        $ret2 = $sql2->rowCount();
                        
                            if($ret2 == 0) {
                                rename('appInstallDone.php','appInstall.php');
                                echo'reload';
                            } else {
                                echo'Login inv&aacute;lido.';
                            }
                        
                        unset($sql2,$ret2);
                    }

                unset($sql,$ret);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$lock,$filtro,$cfg,$pdo,$e);
?>