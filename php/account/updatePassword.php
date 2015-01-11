<?php
	session_start();
	include("../includes/settings.php");
	$oldPass = $_POST["oldpassword"];
	$newPass = $_POST["newpassword"];
	$ControlPanel->updateUserPassword($oldPass, $newPass);
?>