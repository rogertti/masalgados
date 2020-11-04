<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }
        
    /* CONTROLE DE VARIAVEL */

    $msg = "Campo obrigat&oacute;rio vazio.";

        if(empty($_POST['rand'])) { die("Vari&aacute;vel de controle nula."); }
        if(empty($_POST['praca'])) { die($msg); } else { $filtro = 1; }
        if(empty($_POST['nome'])) { die($msg); } else {
            $filtro++;

            $_POST['nome'] = str_replace("'","&#39;",$_POST['nome']);
            $_POST['nome'] = str_replace('"','&#34;',$_POST['nome']);
            $_POST['nome'] = str_replace('%','&#37;',$_POST['nome']);
        }
        #if(!empty($_POST['cpf'])) { $documento = $_POST['cpf']; $filtro++; }
        #if(!empty($_POST['cnpj'])) { $documento = $_POST['cnpj']; $filtro++; }
        if($_POST['pessoa'] == 'F') { $documento = $_POST['cpf']; $filtro++; }
        if($_POST['pessoa'] == 'J') { $documento = $_POST['cnpj']; $filtro++; }

        if($filtro == 3) {
            try {
                include_once('appConnection.php');
                
                /* CONTROLE DE DUPLICATAS */

                $sql = $pdo->prepare("SELECT idcliente,monitor FROM cliente WHERE cpf_cnpj = :cpf_cnpj");
                $sql->bindParam(':cpf_cnpj', $documento, PDO::PARAM_STR);
                $sql->execute();
                $ret = $sql->rowCount();

                    if($ret > 0) {
                        #die('Esse cliente j&aacute; est&aacute; cadastrado.');
                        $lin = $sql->fetch(PDO::FETCH_OBJ);
                        $py = md5('idcliente');
                        
                            if($lin->monitor == 'T') {
                                die('Esse cliente j&aacute; est&aacute; cadastrado.');
                            }
                            
                            if($lin->monitor == 'F') {
                                die('Esse cliente j&aacute; est&aacute; cadastrado, mas est&aacute; desativado. <a href="clienteActivate.php?'.$py.'='.$lin->idcliente.'" title="Ativar o cadastro do cliente">Clique para ativar.</a>');    
                            }
                    }

                unset($sql,$ret,$lin,$py);

                /* INSERE NO BANCO */

                $monitor = 'T';
                $sql = $pdo->prepare("INSERT INTO cliente (praca_idpraca,nome,cpf_cnpj,cep,endereco,bairro,cidade,estado,telefone,celular,email,monitor) VALUES (:praca,:nome,:cpf_cnpj,:cep,:endereco,:bairro,:cidade,:estado,:telefone,:celular,:email,:monitor)");
                $sql->bindParam(':praca', $_POST['praca'], PDO::PARAM_INT);
                $sql->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                $sql->bindParam(':cpf_cnpj', $documento, PDO::PARAM_STR);
                $sql->bindParam(':cep', $_POST['cep'], PDO::PARAM_STR);
                $sql->bindParam(':endereco', $_POST['endereco'], PDO::PARAM_STR);
                $sql->bindParam(':bairro', $_POST['bairro'], PDO::PARAM_STR);
                $sql->bindParam(':cidade', $_POST['cidade'], PDO::PARAM_STR);
                $sql->bindParam(':estado', $_POST['estado'], PDO::PARAM_STR);
                $sql->bindParam(':telefone', $_POST['telefone'], PDO::PARAM_STR);
                $sql->bindParam(':celular', $_POST['celular'], PDO::PARAM_STR);
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
                        $log_descricao = 'Usuário '.$_SESSION['seller'].' cadastrou o cliente '.$_POST['nome'].'.';

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

                unset($sql,$res,$monitor,$documento);
            }
            catch(PDOException $e) {
                echo'Falha ao conectar o servidor '.$e->getMessage();
            }
        } //if filtro

    unset($msg,$pdo,$e,$filtro);
?>