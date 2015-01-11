<?php
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	session_start();
	include("../includes/settings.php");
	$userToken = $_POST["userid"];
	if(!$_SESSION["userToken"] == $userToken){
		die();
	}else {
		$token = $_POST["token"];
		if($token == $_SESSION["token"]) {
			$ControlPanel->getAllUserBackups($userToken);	
		}
	} //end else
?>