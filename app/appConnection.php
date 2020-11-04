<?php
	define("DB_TYPE","mysql");
	define("DB_HOST","localhost");
	define("DB_USER","root");
	define("DB_PASS","root");
	define("DB_DATA","masalgados");

	$pdo = new PDO("".DB_TYPE.":host=".DB_HOST.";dbname=".DB_DATA."",DB_USER,DB_PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec("SET NAMES utf8");
?>