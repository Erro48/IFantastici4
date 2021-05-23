<?php

	define('DB_SERVER', 'localhost');
 	define('DB_USERNAME', 'root');
 	define('DB_PASSWORD', '');	//c3VibWVldGlzYW1hemluZw==
 	define('DB_DATABASE', 'fanta_f1');

	$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD/*base64_decode(DB_PASSWORD)*/, DB_DATABASE);

	if($db->connect_error){
		die("Database error " .  $db->connect_error);
	}
?>
