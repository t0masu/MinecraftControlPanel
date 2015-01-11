<?php
	session_start(); //start session
	include("../../includes/settings.php"); //setup settings
	$ControlPanel->searchPluginCatergory($_POST['cat'], $_POST['page']); //fire function
?>