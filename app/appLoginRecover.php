<?php
    require_once('appConfig.php');

    function decrypt($data, $k) {
        $l = strlen($k);
        
            if ($l < 16)
                $k = str_repeat($k, ceil(16/$l));
                $data = base64_decode($data);
                $val = openssl_decrypt($data, 'AES-256-OFB', $k, 0, $k);
        
        return $val;
    }

    //controle de variável
    $msg = "Campo obrigat&oacute;rio vazio.";
    $key = base64_encode('cripta');

        if(empty($_POST['rand'])) {die("Vari&aacute;vel de controle nula."); }
        if(!empty($_POST['email'])) {
            try {
                include_once('appConnection.php');

                //buscando a senha na tabela login
                $sql = $pdo->prepare("SELECT idvendedor,nome,senha FROM vendedor WHERE email = :email");
                $sql->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        $lin = $sql->fetch(PDO::FETCH_OBJ);

                        //enviando a senha por email
                        require_once('phpmailer/PHPMailerAutoload.php');

                        $mail = new PHPMailer;
                        $mail->CharSet = "UTF-8";

                        $mail->IsSMTP();
                        $mail->Host = "smtp.hostinger.com.br";
                        $mail->SMTPAuth = true;
                        $mail->Username = 'contato@salgadosmarlonantonio.com.br';
                        $mail->Password = 'sma2152';
                        #$mail->SMTPSecure = "ssl";
                        $mail->Port = "587";

                        $mail->setFrom('contato@salgadosmarlonantonio.com.br', 'MA - Não responda');
                        $mail->addAddress($_POST['email']);
                        $mail->addReplyTo($_POST['email'], 'MA');
                        $mail->Subject = 'Recupere sua senha de acesso ao programa de orçamentos';
                        $mail->IsHTML(true);
                        $mail->Body = 'A sua senha é <strong>'.base64_decode(decrypt($lin->senha, $key)).'</strong>';
                        $sent = $mail->Send();
                        $mail->ClearAllRecipients();
                        $mail->ClearAttachments();

                            if(!$sent) {
                                die('A senha n&atilde;o foi enviada. '.$mail->ErrorInfo);
                            } else {
                                echo'true';
                            }

                        //Registrando LOG

                        $log_datado = date('Y-m-d');
                        $log_hora = date('H:i:s');
                        $log_descricao = 'Usuário '.$lin->nome.' recuperou a senha.';

                        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
                        $sql_log->bindParam(':idvendedor', $lin->idvendedor, PDO::PARAM_INT);
                        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
                        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
                        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
                        $res_log = $sql_log->execute();

                            if(!$res_log) {
                                var_dump($sql_log->errorInfo());
                            }

                        unset($lin,$nome,$remetente,$assunto,$header,$conteudo,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
                    } else {
                        echo'Esse email n&atilde;o &eacute; de um usu&aacute;rio cadastrado.';
                    }

                unset($sql,$ret);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        }

    unset($pdo,$e,$msg,$key,$cfg);
?>