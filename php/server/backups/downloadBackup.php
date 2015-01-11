<?php
	session_start();
	include("../../includes/settings.php"); //settings file
	$filename = $_POST["filename"]; //filename from form
	$uuid = $_POST["serverid"]; //serverid from form
	if(substr($filename, -4) == ".tar") {
		$uuidTokenComparison = $ControlPanel->uuidTokenComparison($uuid);
		if($uuidTokenComparison[1] == 1) {
			$row = $uuidTokenComparison[0]->fetch();
			$path_parts = pathinfo($filename);
			$file = $path_parts["basename"];
			if(!$connection = ssh2_connect($row["sshHost"], $row["sshPort"])) {
				throw new Exception("Could not connect to remote server");
			}
			ssh2_auth_password($connection, $row["sshUser"], $row["sshPass"]);
			$sftp = ssh2_sftp($connection); //sftp to remote server
			if(substr($row["serverPath"], -1) == "/") {
				$path = $row["serverPath"] . "backups/" . $filename;
			} else {
				$path = $row["serverPath"] . "/backups/" . $filename;
			}
			set_time_limit(0); //set script time limit to 0
			$file = "ssh2.sftp://$sftp/$path"; //file on remote server
			header("Content-Length:" . filesize($file));
			header("Content-Type: application/tar"); //content type set
			header("Content-Type: octet-stream"); //octet stream
			header("Content-Disposition: attachment; filename=".$filename);
			header("Content-Transfer-Encoding: binary"); //Encoding set
			ob_end_clean();
			readfile($file);
			exit();
		} //end uuidTokenComparison
	}
?>