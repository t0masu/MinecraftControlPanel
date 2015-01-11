<?php
	session_start();
	include("../../includes/settings.php"); //settings file
	$ControlPanel->getServerPluginsList($_POST['id']);
?>