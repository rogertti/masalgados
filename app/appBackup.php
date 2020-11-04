<?php
    require_once('appConfig.php');

        if(empty($_SESSION['key'])) {
            header ('location:./');
        }

    try {
        include_once('appConnection.php');

        $return = '';
        $tables = array();
        $sql = $pdo->query("SHOW TABLES");

            while($row = $sql->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            //cycle through each table and format the data
            foreach($tables as $table) {
                $sql2 = $pdo->query("SELECT * FROM ".$table);
                $num_fields = $sql2->columnCount();

                $sql3 = $pdo->query("SHOW CREATE TABLE ".$table);
                $row2 = $sql3->fetch(PDO::FETCH_NUM);
                $return.= "\n".$row2[1].";\n";

                    for($i = 0; $i < $num_fields; $i++) {
                        while($row3 = $sql2->fetch(PDO::FETCH_NUM)) {
                            $return.= 'INSERT INTO '.$table.' VALUES(';

                                for($j = 0; $j < $num_fields; $j++) {
                                    $row3[$j] = addslashes($row3[$j]);
                                    $row3[$j] = str_replace("\n","\\n",$row3[$j]);
                                    #$row3[$j] = ereg_replace("\n","\\n",$row3[$j]);
                                    #$row3[$j] = preg_replace("\n","\\n",$row3[$j]);

                                        if(isset($row3[$j])) {
                                            $return.= '"'.$row3[$j].'"';
                                        }
                                        else {
                                            $return.= '""';
                                        }

                                        if ($j < ($num_fields - 1)) {
                                            $return.= ',';
                                        }
                                } //for

                            $return.= ");\n";
                        } //while
                    }// for

                $return.="";
            } //foreach

        //save the file
        #$file = 'db/'.time().'-'.(md5(implode(',', $tables))).'.sql';
        $file = 'db/'.md5(time()).'.sql';
        $handle = fopen($file,'w+');
        fwrite($handle,$return);
        fclose($handle);

            //creating file to download
            if (file_exists($file)) {
                header('Content-Description: Back up');
                header('Content-Type: application/sql');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: '.filesize($file));
                readfile($file);
            }

        //Registrando LOG

        $log_datado = date('Y-m-d');
        $log_hora = date('H:i:s');
        $log_descricao = 'Usuário '.$_SESSION['seller'].' fez back up do sistema.';

        $sql_log = $pdo->prepare("INSERT INTO log (vendedor_idvendedor,datado,hora,descricao) VALUES (:idvendedor,:datado,:hora,:descricao)");
        $sql_log->bindParam(':idvendedor', $_SESSION['id'], PDO::PARAM_INT);
        $sql_log->bindParam(':datado', $log_datado, PDO::PARAM_STR);
        $sql_log->bindParam(':hora', $log_hora, PDO::PARAM_STR);
        $sql_log->bindParam(':descricao', $log_descricao, PDO::PARAM_STR);
        $res_log = $sql_log->execute();

            if(!$res_log) {
                var_dump($sql_log->errorInfo());
            }

        unset($file,$handle,$return,$tables,$sql,$sql2,$row,$table,$num_fields,$row2,$row3,$sql2,$sql3,$j,$sql_log,$res_log,$log_datado,$log_descricao,$log_hora);
    }
    catch(PDOException $e) {
        echo 'Erro ao conectar o servidor '.$e->getMessage();
    }

    unset($pdo,$e,$cfg);
 ?>