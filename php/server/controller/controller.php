<?php
	/**
		AJAX post to this file from soloserver page
		Check formToken against server set session to check authenticity
	*/
	session_start();
	include("../../includes/settings.php"); //include settings file
	if($_SESSION['formToken'] == $_POST['token']){
		if($_POST['start']){
			//start function
			$ControlPanel->startRemoteServer($_POST['serverid']);
		}else if($_POST['stop']){
			//stop function
			$ControlPanel->stopRemoteServer($_POST['serverid']);
		} //end button submit check
	} //end formToken check
?>