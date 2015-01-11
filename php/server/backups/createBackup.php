<?php
	session_start();
	include("../../includes/settings.php"); //include settings file
	$ControlPanel->createRemoteServerBackup($_POST["serverid"]);
?>	