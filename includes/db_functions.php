<?php

	$db_user = 'REDACTED';
	$db_password = 'REDACTED';
	$db_host = 'localhost';
	$db_name = 't3app';

	// Make the connection:
	return $dbc = @mysqli_connect ($db_host, $db_user, $db_password, $db_name) OR die ('Could not connect to MySQL' );
?>
