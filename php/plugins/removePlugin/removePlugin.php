<?php
	session_start(); //start session
	include("../../includes/settings.php"); //include settings file
	$pluginName = $_POST["filename"]; //plugin to "remove"
	$uuid = $_POST["serverid"]; //uuid to check against
	$ControlPanel->removeRemoteServerPlugin($pluginName, $uuid);
?>