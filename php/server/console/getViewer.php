<?php
	session_start(); //start session for functions that require it
	include("../../includes/settings.php"); //settings file
	$ControlPanel->getConsoleViewer($_POST['id']);
	//echo "Console Viewer";
?>