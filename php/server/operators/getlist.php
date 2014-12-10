<?php
	session_start();
	include("../../includes/settings.php"); //include settings file
	$ControlPanel->getServerOperators($_POST['id']); //launch getServerOperators function to return data to page
?>