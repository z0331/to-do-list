<?php

$dsn = "mysql:host=localhost;dbname=to_do_list;charset=utf8";
$username = "root";
$password = "Skaarjh1!";

//Try&catch for connection to database
try {
	$dbh = new PDO($dsn, $username, $password);
	$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	echo "Unable to connect" . "<br>";
	echo $e -> getMessage();
	exit;
}

?>