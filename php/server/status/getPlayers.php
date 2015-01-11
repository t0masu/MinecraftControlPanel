<?php
	session_start();
	include("../../includes/settings.php"); //setting file
	$row = $ControlPanel->getServerStatusById($_POST['id']); //get server info via function
	$query = $ControlPanel->serverStatus($row['serverHost'], $row['serverVersion'], $row['serverPort']); //load server status
	
	if($query) {
		echo  $query['players'] . '/' . $query['maxplayers'];
	}
?>