<?php
	session_start();
	include("../../../php/includes/settings.php"); //include settings file
	$ControlPanel->searchPluginDB($_POST['pluginName']);
?>