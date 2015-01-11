<?php
	session_start();
	include("../../includes/settings.php"); //settings file
	$ControlPanel->getServerBackupsList($_POST['id']); //call backup list function for server
?>