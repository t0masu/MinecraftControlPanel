<?php
	session_start();
	include("../../includes/settings.php"); //setting file
	$row = $ControlPanel->getServerStatusById($_POST['id']); //get server info via function
	$query = $ControlPanel->serverStatus($row['serverHost'], $row['serverVersion'], $row['serverPort']); //load server status
	
	if($query) {
		echo "Status: ";
		echo '<span class="label label-success">Online</span>&nbsp;'; //server online
		echo '<span class="label label-warning" id="players">' . $query['players'] . '/' . $query['maxplayers'] .  '</span>';
	}else {
		echo "Status: ";
		echo '<span class="label label-danger">Offline</span>'; //server offline
	} //end if|else
?>