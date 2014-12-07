<?php
	//error_reporting(E_ALL);
	//ini_set("display_errors",1);
	session_start();
	include("php/includes/settings.php");
	if($_SESSION['userToken']){
		if($_GET['p']){
			switch($_GET['p']){
				case "csj":
					include("php/views/csj.php");
					break;
				case "backups":
					include("php/views/backups.php");
					break;
				case "plugins":
					include("php/views/plugins.php");
					break;
				case "server":
					include("php/views/soloserver.php");
					break;
				case "servers":
					include("php/views/servers.php");
					break;
				case "settings":
					include("php/views/settings.php");
					break;
			}
		}else {
			include("php/views/dashboard.php");
		}
	}else if(!$_SESSION['userToken']){
		include("php/views/loginPage.php");
	}
?>