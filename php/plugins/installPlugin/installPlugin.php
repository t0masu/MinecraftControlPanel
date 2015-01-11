<?php
	session_start(); //start session
	include("../../includes/settings.php"); //setup settings
	$ControlPanel->addRemoteServerPlugin($_POST["clientServer"], $_POST["slug"], $_POST["pluginVersion"]);
	echo "Plugin has been downloaded and installed!";
?>