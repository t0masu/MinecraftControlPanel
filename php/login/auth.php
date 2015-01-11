<?php
	session_start();
	include(__DIR__ . '/../includes/settings.php'); //instatiate the class with settings
	$username = $_POST['username'][0]; //set username variable, sidenote [0] is to correct it sending as an array -.-
	$password = $_POST['password'][0]; //set password variable, ^^
	$num = $ControlPanel->parceLogin($username, $password); //send off to function for reply
	echo $num;
?>	