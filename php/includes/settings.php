<?php
	include(__DIR__ . '/../../php/class/main.php');
	$ControlPanel = new ControlPanel("127.0.0.1", "DATABASE NAME", "DATABASE USER", "DATABASE PASSWORD"); //client customisable HOST DBNAME DBUSER DBPASS
	$data = $ControlPanel->getSettings(); //GET CPANEL SETTINGS IN ONE GO!
	//define PUBKEY AND PRIVKEY
	define("__PUBKEY__", $_SERVER["DOCUMENT_ROOT"] . "/" . $data["cpanel_pubkey_path"]); //PUBKEY FOR ENCRYPT
	define("__PRIVKEY__",$_SERVER["DOCUMENT_ROOT"] . "/" . $data["cpanel_privkey_path"]); //PRIVKEY FOR DECRYPT
?>