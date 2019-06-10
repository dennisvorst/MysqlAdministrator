<?php
	session_start();

	/* push a bunch of messages into a cookie and then go back */
	$messages = [];
	$messages[] = "Het is nmu precies : " . date('l jS \of F Y h:i:s A');

	$_SESSION['messages'] = $messages;
	header("Location: index.php");

?>