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
    $key = base64_encode('cripta');

        //depurando os campos
        if(empty($_POST['rand'])) { die('Vari&aacute;vel de controle nula.'); }
        if(empty($_POST['nome'])) { die($msg); } else {
            $filtro = 1;
            $_POST['nome'] = str_replace("'","&#39;",$_POST['nome']);
            $_POST['nome'] = str_replace('"','&#34;',$_POST['nome']);
            $_POST['nome'] = str_replace('%','&#37;',$_POST['nome']);
        }
        if(empty($_POST['usuario'])) { die($msg); } else {
            $filtro++;
            $usuario = encrypt($_POST['usuario'], $key);
        }
        if(empty($_POST['senha'])) { die($msg); } else {
            $filtro++;
            $senha = encrypt($_POST['senha'], $key);
        }
        if(empty($_POST['email'])) { die($msg); } else {
            $filtro++;
        }

        if($filtro == 4) {
            try {
                include_once('appConnection.php');

                //insere no banco
                $tipo = 'R';
                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO vendedor (nome,usuario,senha,email,tipo,monitor) VALUES (:nome,:usuario,:senha,:email,:tipo,:monitor)");
                $sql->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                $sql->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $sql->bindParam(':senha', $senha, PDO::PARAM_STR);
                $sql->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $sql->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                $sql->bindParam(':monitor', $monitor, PDO::PARAM_STR);
                $res = $sql->execute();

                    if(!$res) {
                        var_dump($sql->errorInfo());
                        exit;
                    } else {
                        echo'true';
                        rename('appInstall.php','appInstallDone.php');
                    }

                unset($sql,$res,$usuario,$senha,$tipo,$monitor);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } else {
            die('Algum campo n&atilde;o foi validado.');
        }

    unset($pdo,$msg,$key,$filtro,$cfg,$e);
?>