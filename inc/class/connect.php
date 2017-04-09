<?php

$dsn = "DATABASE";
$username = "USERNAME";
$password = "PASSWORD";

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