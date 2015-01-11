<?php
	class ControlPanel {
		protected $datab;
		public $isConnected;
		public $owner;
		
		public function __construct($host, $dbname, $username, $password, $options=array()) {
			$this->isConnected=true; //set connection status to true
			$this->owner = $_SESSION['userToken']; //set owner to user auth token
			$timeout = $timeout;
			try {
				$this->datab = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
					//database initalised
				$this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //attributes set
				$this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //ditto ^^
			} catch(Exception $e) {
				$this->isConnected=false;
				throw new Exception($e->getMessage());
			}
		} //end of constructor
		
		public function closeDB() {
			$this->datab = null; //database closed
			$this->isConnected = false; //status set
		} //end of closeDB function
		
		public function getSettings() {
			$st = $this->datab->prepare("SELECT * FROM cpanel_settings"); //cpanel settings selected
			$st->execute(); //execute sql
			$row = $st->fetch(); //fetch rows
			return $row;
		} //end of getSettings function
		
		public function accountInfo() {
			$st = $this->datab->prepare("SELECT * FROM cpanel_users WHERE username = ?");
			$st->bindParam(1, $_SESSION["userToken"]); //set ? = $_SESSION["userToken"]
			$st->execute(); //execute query
			$row = $st->fetch();
			return $row;
		} //end of accountInfo function
		
		//Start Encrypt/Decrypt functions
		
		public function encryptData($data) {
			$key = fopen(__PUBKEY__, "r"); //open key
			$pub_key = fread($key, 2048); //read key
			fclose($key); //close key
			$key = openssl_get_publickey($pub_key); //public key loaded
			$encrypted_data = openssl_public_encrypt($data, $output, $pub_key); //encryption takes place...
			return $output; //encrypted data returned
		} //end of encryptData function
		
		public function decryptData($data) {
			$key = fopen(__PRIVKEY__, "r"); //open in read only
			$priv_key = fread($key, 2048); //read private key
			fclose($key); //close priv_key
			$decrypted_data = openssl_private_decrypt($data, $output, $priv_key); //decryption takes place...
			return $output; //return data decrypted ;P
		} //end of decryptData function
		
		/*
		 *	End Encrypt/Decrypt functions
		*/
		
		/*
		 *	Start Minecraft Server Status functions
		*/
		
		private function serverConnect($host, $port) {
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); //create socket
			$timeout = array(
						"sec"  => $timeout,
						"usec" => $timeout * 100000
						);
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);
			socket_connect($socket, $host, $port); //connect to server
			return $socket; //return socket to serverStatus
		} //end of serverConnect
		
		private function serverDisconnect($socket) {
			if($socket !=null){
				socket_close($socket); //close socket
			}
		} //end of serverDC
		
		private function readPacketLength($socket) {
			$a = 0;
			$b = 0;
			while(true){
				$c = socket_read($socket, 1);
				if(!$c){
					return 0;
				}
				$c = Ord($c);
				$a |= ($c & 0x7F) << $b++ * 7;
				if($b > 5){
					return false;
				}
				if(($c & 0x80) !=128){
					break;
				}
			}
			return $a;
		} //end of readPacketLength
		
		public function serverStatus($host, $version, $port = 25565) {
			if(substr_count($host, '.') !=4){
				$host = gethostbyname($host); //get hostname change to ip if four octets of an ip are not read
			}
			$timeout = 3; //set timeout in seconds for server response
			$ServerData = array(
				"hostname" 		=> 	$host,
				"version" 		=>	false,
				"protocol"		=>	false,
				"players"		=>	false,
				"maxplayers"	=>	false,
				"motd"			=>	false,
				"motd_raw"		=>	false,
				"favicon"		=>	false,
				"ping"			=>	false,
			); //setup array to hold data and return data at end of function
			$socket = $this->serverConnect($host, $port); //call function to connect to server
			
			if(!$socket){
				return false; //if no socket then finish here
			}
			
			if(preg_match('/1.7\/1.8/', $version)) {
				$start = microtime(true); //start timer
				$handshake = pack('cccca*', hexdec(strlen($host)), 0, 0x04, strlen($host), $host)
							. pack('nc', $port, 0x01); //craft ping/handshake packet
				socket_send($socket, $handshake, strlen($handshake), 0); //send handshake
				socket_send($socket, "\x01\x00", 2, 0); //send ping
				socket_read($socket, 1); //read data from socket
				
				$ping = round((microtime(true)-$start)*1000); //calculate ping
				$packetLength = $this->readPacketLength($socket); //calculate packet length
				if($packetLength < 10){
					return false; //if its less than 10 then is not a valid response
				}
				
				$data = json_decode($data); //minecraft packs it as a json response
				$ServerData["version"] = $data->version->name; //set game version
				$ServerData["protocol"] = $data->version->protocol; //set protocol version
				$ServerData["players"] = $data->players->online; //set players online
				$ServerData["maxplayers"] = $data->players->max; //set max players online
				
				$motd = $data->description; //set description
				$motd = preg_replace("/(§.)/", "", $motd); //remove special chars
				$motd = preg_replace("/[^[:alnum:][:punct:] ]/", "", $motd); //remove other special chars
				
				$ServerData["motd"] = $motd; //final motd after processing is sent to array
				$ServerData["motd_raw"] = $data->description; //set raw motd
				$ServerData["favicon"] = $data->favicon; //favicon is sent to array
				$ServerData["ping"] = $ping; //ping is sent to array
			}else {
				//other protocol and versions below 1.7+
				$start = microtime(true); //start timer
				socket_send($socket, "\xFE\x01", 2, 0); //send ping
				$length = socket_recv($socket, $data, 512, 0); //check length of recieved packet/packets
				$ping = round((microtime(true)-$start)*1000); //calculate ping
				
				if($length < 4 || $data[0] != "\xFF"){
					return false; //if packet length is less than 4 then its not valid
				}
				
				if(substr((String)$data, 3, 5) == "\x00\xa7\x00\x31\x00") {
					$result = explode("\x00", mb_convert_encoding(substr((String)$data, 15)
											  , "UTF-8", "UCS-2")); //convert from UCS-2 to UTF-8
					$motd = $result[1]; //send final motd to variable
					$motdraw = $motd;	//set raw motd data to variable
				}else {
					$result = explode("§", mb_convert_encoding(substr((String)$data, 3)
										 , "UTF-8", "UCS-2")); //convert from UCS-2 to UTF-8
					foreach($result as $key => $value){
						if($key !=sizeof($result)-1 && $key !=sizeof($result)-2 && $key !=0) {
							$motd .= '§'.$value; //filter through and set to variable
						}
					}
					$motdraw = $motd; //set raw motd data to variable
				} //end of else statement substr((String)$data ...
				$motd = preg_replace("/(§.)/", "", $motd); //remove colour chars and other special chars
				$motd = preg_replace("/[^[:alnum:][:punct:] ]/", "", $motd); //remove something cant remember what xD
				
				$ServerData["version"] = $result[0]; //final version set
				$ServerData["players"] = $result[sizeof($result)-2]; //final players set
				$ServerData["maxplayers"] = $result[sizeof($result)-1]; //final max players set
				$ServerData["motd"] = $motd; //final motd set
				$ServerData["motd_raw"] = $motdraw; //final raw motd data set
				$ServerData["ping"] = $ping; //final ping set
			} //end of else statement preg_match
			$this->serverDisconnect($socket); //disconnect socket
			return $ServerData; //return data gathered
		} //end of serverStatus function
		
		//End Minecraft Server Status functions
		
		public function UUIDTokenComparison($uuid){
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ? and uuid = ?"); //prepare the sql statement
			$st->bindParam(1, $this->owner); //bind arg 1 in the statement
			$st->bindParam(2, $uuid); //bind arg 2 in the statement
			$st->execute(); //execute the sql query
			$count = $st->rowCount(); //count the returned result; expected result = 1
			return array($st, $count); //return $count and $st to called script/function
		} //end of UUIDTokenComparison function
		
		public function generateServerList(){
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?"); //preapre sql statement to select all of the users servers
			$st->bindParam(1, $this->owner); //bind arg 1 to statement
			$st->execute(); //execute the sql statement
			while($row = $st->fetch()){
				?>
					<tr>
						<td>
							<a href="/server&id=<?=htmlspecialchars($row['uuid']);?>"><?=substr($row['uuid'], 0, 12);?></a>
						</td>
						<td>
							<?=$row['serverName'];?>
						</td>
						<td>
							<?=substr($row['serverHost'],0,18);?>
						</td>
						<td>
							<?=$row['serverPort'];?>
						</td>
						<td>
							MC <?=$row['serverVersion'];?>
						</td>
						<td>
							<a href="/server&id=<?=$row['uuid'];?>"><i class="glyphicon glyphicon-cog"></i></a>
						</td>
					</tr>
				<?php
			}
		
		} //end of generateServerList function
		
		public function sendCommandtoRemoteServer($host, $username, $password, $port, $command){
			$connection = ssh2_connect($host, $port);
			ssh2_auth_password($connection, $username, $password);
			$command = ssh2_exec($connection, $command);
			return $command;
		} //end of sendCommandtoRemoteServer
		
		public function getServerPluginsList($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid); //get comparison and get data array
			if($uuidTokenComparison[1] == 1){
				$row = $uuidTokenComparison[0]->fetch(); //fetch rows
				if(!$connection = ssh2_connect($row['sshHost'], $row['sshPort'])) {
					throw new Exception("Could not connect");
				}
				ssh2_auth_password($connection, $row['sshUser'], $row['sshPass']);
				$sftp = ssh2_sftp($connection);
				$path = $row['serverPath']; //set the server path on remote server
				if(substr($path, -1) == "/"){
					$path = $path . "plugins/"; //add plugins/ to the path
				}else {
					$path = $path . "/plugins/"; // add /plugins/ if the original path doesn't end in /
				}
				$directoryHandle = opendir("ssh2.sftp://$sftp/$path"); //open remote directory on a handle
				while(false !==($file = readdir($directoryHandle))){
					if($file != "." && $file != ".."){ //filter out the . and .. in a linux filesystem
						if(substr($file, -4) == ".jar"){
							$jars[] = $file; //jar files only included in the array
						}
					}
				} //end while loop
				if(count($jars) == 0) {
					?>
						<tr>
							<td>You have no plugins!</td>
						</tr>
					<?php
				}
				for($i = 0; $i < count($jars);){
						?>
							<tr>
								<td><?=$jars[$i];?></td>
								<td>
									<form action="/php/plugins/removePlugin/removePlugin.php" method="POST" id="removePlugin<?=$i;?>">
										<input type="hidden" name="filename" value="<?=$jars[$i];?>" />
										<input type="hidden" name="serverid" value="<?=$uuid;?>" />
										<button class="btn btn-danger" id="removeBTN<?=$i;?>"><i class="glyphicon glyphicon-minus"></i></button>
									</form>
									<script>
										$("#removePlugin<?=$i;?>").ajaxForm();
										$("#removeBTN<?=$i;?>").click(function() {
			alert("Removing plugin");
			setTimeout(function() {
				$.post("/php/server/operators/getPlugins.php", {id:"<?=$uuid;?>"}, function(data){
					$("#plugins").hide();
					$("#plugins").html(data).fadeIn(1200);
				});
			}, 2000);
		});
									</script>
								</td>
							</tr>
						<?php
					$i++;
				} //end for loop
				unset($connection);
			} //end of if
		} //end of getServerPluginsList function
		
		public function addRemoteServerPlugin($uuid, $slug, $version) {
			$uuidTokenComparison = $this->uuidTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1) {
				$row = $uuidTokenComparison[0]->fetch(); //fetch rows
				$ch = curl_init(); //curl started
				curl_setopt($ch, CURLOPT_URL, "http://api.bukget.org/3/plugins/bukkit/$slug"); //curl url
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //return tranfer
				$output = curl_exec($ch); //execute curl request
				$output = json_decode($output, true); //decode the json data returned by BukGet
				$i = 0;
				while($i < count($output["versions"])) {
					if($version == $output["versions"][$i]["version"]) {
						$downloadUrl = $output["versions"][$i]["download"];
						$filename = $output["versions"][$i]["filename"];
						break 1;
					}
					$i++;
				} //end while
				$path = $row['serverPath'];
				if(substr($path, -1) != "/") {
					$path = $path . "/plugins/";
				} else {
					$path = $path . "plugins/";
				}
				$connection = ssh2_connect($row['sshHost'], $row['sshPort']); //connect to remote server
				ssh2_auth_password($connection, $row['sshUser'], $row['sshPass']); //authenticate with the remote server *note* need to make an ssh key version of the auth *endnote*
				if(substr($filename, -4) == ".zip") {
					$command = "wget -O " . $path . $filename . " " . $downloadUrl . " && screen -dmS unzipPlugin unzip " . $path . $filename . " -d " . $path;
					$com = ssh2_exec($connection, $command);
				} else {
					$command = "wget -O " . $path . $filename . " " . $downloadUrl;
					$com = ssh2_exec($connection, $command);
				}
					echo "Plugin Downloaded!";
			} //end uuidTokenComparison
		} //end of addRemoteServerPlugin function
		
		public function removeRemoteServerPlugin($pluginName, $uuid) {
			$uuidTokenComparison = $this->uuidTokenComparison($uuid); //uuid token comparison
			if($uuidTokenComparison[1] == 1) {
				$row = $uuidTokenComparison[0]->fetch(); //fetch rows
				if(!$connection = ssh2_connect($row["sshHost"], $row["sshPort"])) {
					throw new Exception("Could not connect");
				}
				ssh2_auth_password($connection, $row["sshUser"], $row["sshPass"]);
				$path = $row["serverPath"]; //server path
				if(substr($path, -1) == "/") {
					$path = $path . "plugins/";
				} else {
					$path = $path . "/plugins/";
				}
				$command = "mv " . $path . $pluginName . " " . $path . $pluginName . ".old";
				$execute = ssh2_exec($connection, $command); //execute command
				unset($connection); //unset ssh connection var
			} //end uuidTokenComparison
		} //end of removeRemoteServerPlugin function *note* this function only changes the plugin extension to something not renderable by the server e.g Plugin.jar => Plugin.jar.disabled *endnote*
		
		public function getServerBackupsList($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid); //get comparison and get data array
			if($uuidTokenComparison[1] == 1) { //check the validity of the request
				$row = $uuidTokenComparison[0]->fetch(); //fetch rows from the database
				$connection = ssh2_connect($row['sshHost'], $row['sshPort']);
				ssh2_auth_password($connection, $row['sshUser'], $row['sshPass']);
				$sftp = ssh2_sftp($connection);
				$path = $row['serverPath'];
				if(substr($path, -1) == "/"){
					$path = $path . "backups/"; // add / to the directory loaded from the database
				}else {
					$path = $path . "/backups/"; // add / and / to the directory loaded from the database
				}
				$directoryHandle = opendir("ssh2.sftp://$sftp/$path");
				while(false !==($file = readdir($directoryHandle))){
					if($file != "." && $file != ".."){ 
						if(substr($file, -4) == ".zip" || ".tar"){
							$backups[] = $file;
						}
					}
				}//end while loop
				unset($connection); //end connection to free memory
				
				if(!$backups){
					?><tr><td>No server backups</td></tr><?php
				}
				
				for($i = 0; $i < count($backups);) {
						?>
							<tr>
								<td><?=$i + 1;?></td>
								<td><?=$backups[$i];?></td>
								<td>
									<form action="/php/server/backups/downloadBackup.php" method="POST" id="downloadBackup">
										<input type="hidden" name="serverid" value="<?=$uuid;?>" />
										<input type="hidden" name="filename" value="<?=$backups[$i];?>" />
										<input type="submit" value="Download" class="btn btn-success" />
									</form>
								</td>
							</tr>
						<?php
					$i++;
				} //end for
			}
		}// end of getServerBackupsList function
		
		public function getAllUserBackups($userToken) {
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?");
			$st->bindParam(1, $userToken); //bind $userToken to ?
			$st->execute(); //execute query
			$path = $row["serverPath"]; //server path
			if(substr($path, -1) == "/"){
				$path = $path . "backups/"; // add / to the directory loaded from the database
			}else {
				$path = $path . "/backups/"; // add / and / to the directory loaded from the database
			}
			while($row = $st->fetch()) {
				if(!$connection = ssh2_connect($row["sshHost"], $row["sshPort"])) {
					throw new Exception();
				}
				ssh2_auth_password($connection, $row["sshUser"], $row["sshPass"]); //auth
				$sftp = ssh2_sftp($connection); //sftp
				$directoryHandle = opendir("ssh2.sftp://$sftp/$path");
				while(false !==($file = readdir($directoryHandle))){
					if($file != "." && $file != ".."){ 
						if(substr($file, -4) == ".zip" || ".tar"){
							$backups[] = $file;
						}
					}
				}//end while loop
				unset($connection);
				?>
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">
								<?=$row["serverName"];?>
							</h3>
						</div>
						<div class="box-body">
							<table class="table table-hover">
							<tbody>
							<?php
								for($i = 0; $i < count($backups);) {
									?>
										<tr>
											<td><?=$i + 1;?></td>
											<td><?=$backups[$i];?></td>
											<td>
												<form action="/php/server/backups/downloadBackup.php" method="POST" id="downloadBackup">
													<input type="hidden" name="serverid" value="<?=$row["uuid"];?>" />
													<input type="hidden" name="filename" value="<?=$backups[$i];?>" />
													<input type="submit" value="Download" class="btn btn-success" />
												</form>
											</td>
										</tr>
									<?php
								}
							?>
							</tbody>
							</table>
						</div>
						<div class="box-footer">
						
						</div>
					</div>
				<?php
				
			} //end while
		} //end getAllUserBackups function
		
		public function createRemoteServerBackup($uuid) {
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1){
				$row = $uuidTokenComparison[0]->fetch(); //fetch all releveant rows from the database
				$connection = ssh2_connect($row["sshHost"], $row["sshPort"]);
				ssh2_auth_password($connection, $row["sshUser"], $row["sshPass"]);
				$path = $row['serverPath'];
				if(substr($path, -1) != "/"){
					$path = $path . "/";
				}
				$bkpath = $path . "backups/"; //set bkpath
				
				
					$command = "tar -cvf " . $bkpath . $uuid . "_backup" . date("Y-m-d_H:i:s") . ".tar " . $path . "*  --exclude=" . basename($bkpath);
					$execute = ssh2_exec($connection, $command);
					echo $command;
					if($execute){
						return true;
					}else {
						return false;
					}
				//unset($connection);
			}
		} //end of createRemoteServerBackup function
		
		public function getServerOperators($uuid) {
			$uuidTokenComparison = $this->uuidTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1) {
				$row = $uuidTokenComparison[0]->fetch();
				$path = $row['serverPath'];
				$version = $row['serverVersion'];
				$connection = ssh2_connect($row['sshHost'], $row['sshPort']);
				ssh2_auth_password($connection, $row['sshUser'], $row['sshPass']);
				$sftp = ssh2_sftp($connection);
				if(preg_match("/1.7|1.8/", $version)) {
					if(substr($row['serverPath'], -1) == "/"){
						$path = $path . "ops.json"; // add / to the directory loaded from the database
					}else {
						$path = $path . "/ops.json"; // add / and / to the directory loaded from the database
					}
					$file = file_get_contents("ssh2.sftp://$sftp/$path");
					$file = json_decode($file, 1);
					
					for($i = 0; $i < count($file);){
						?>
							<tr>
								<td><?=$file[$i]['name'];?></td>
							</tr>
						<?php
						$i++;
					} //end for loop
					
					if(count($file) == 0) {
						?>
							<tr>
								<td>No server operators found</td>
							</tr>	
						<?php
					}
				} //end preg_match
				unset($connection);
			} //end uuidTokenComparison check
		} //end of getServerOperators function
		
		public function getConsoleViewer($uuid) {
			$uuidTokenComparison = $this->uuidTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1){
				$row = $uuidTokenComparison[0]->fetch(); //fetch rows from db relating to this server
				$path = $row['serverPath']; //server path variable
				$version = $row['serverVersion'];
				
				if(substr($path, -1) != "/"){
					$path = $path . "/";
				} //filter path
				
				if(preg_match("/1.7|1.8/", $version)) {
					$path = $path . "logs/latest.log";
				} else {
					$path = $path . "server.log";
				} //end preg_match	
				
				//connect to server over ssh
				$connection = ssh2_connect($row['sshHost'], $row['sshPort']);
				ssh2_auth_password($connection, $row['sshUser'], $row['sshPass']);
				$sftp = ssh2_sftp($connection);
				
				$file = file_get_contents("ssh2.sftp://$sftp/$path");
				$file = explode("\n\r", $file);
				unset($connection);
				
				?><pre style="overflow-y: scroll; height: 300px;"><?php
				
				foreach($file as $line) {
					?>
						<p style="font-size: 14px; font-family: Tahoma;"><?=trim($line)."\n";?></p>
					<?php
				} //end foreach
				
				?></pre><?php
			} //end uuidTokenComparison check
		} //end getConsoleViewer function
		
		public function addSelfManagedServer(){} //end of addSelfManagedServer function
		
		public function createRemoteServer(){} //end of createRemoteServer function
		
		public function startRemoteServer($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1){
				$row = $uuidTokenComparison[0]->fetch();
				$path = $row['serverPath'];
				$command = $path . "/start.sh start";
				$command = $this->sendCommandtoRemoteServer($row['sshHost'], $row['sshUser'], $row['sshPass'], $row['sshPort'], $command);
				if($command){
					return true;
				}else {
					return false;
				}
			}
		} //end of startRemoteServer function
		
		public function stopRemoteServer($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid);
			if($uuidTokenComparison[1] == 1){
				$row = $uuidTokenComparison[0]->fetch();
				$path = $row['serverPath'];
				$command = $path . "/start.sh stop";
				$command = $this->sendCommandtoRemoteServer($row['sshHost'], $row['sshUser'], $row['sshPass'], $row['sshPort'], $command);
				if($command){
					return true;
				}else {
					return false;
				}
			}
		} //end of startRemoteServer function
		
		public function parceLogin($username, $password) {
			$sql = $this->datab->prepare("SELECT * FROM cpanel_users WHERE username = ?");
			$sql->bindParam(1, $username);
			$sql->execute();
			$count = $sql->rowCount();
			if($count != 1) {
				echo "0"; //echo 0 to script for ajax call
			} else if($count == 1) {
				$row = $sql->fetch(); //fetch rows for password comparison
				$encPass = base64_decode($row["password"]); //encrypted password
				$decPass = $this->decryptData($encPass); //decrypt pass in db
				if($password === $decPass) {
					echo "1"; //password is correct after decryption
					$_SESSION["userToken"] = $username;
				}
			}
		} //end of parceLogin function
		
		
		public function searchPluginDB($slug) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.bukget.org/3/search/slug/like/$slug?fields=description,plugin_name,slug,stage,versions"); //get request to bukget with $slug attached
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch); //execute get request
			if(curl_errno($ch)) {
				echo "Curl Error: " . curl_error($ch);
			}
			$output = json_decode($output, true);
			curl_close($ch);
			$i = 0;
			while($i < count($output)) {
				if($output[$i]["stage"] != "Deleted") {
					?>
						<div class="well">
							<!-- plugin here -->
							<h4><a href="/plugin&slug=<?=$output[$i]["slug"];?>"><?=$output[$i]["plugin_name"];?></a> - 
								<?php
									switch($output[$i]["stage"]) {
										case "Mature":
											echo "<label class='label label-success'>Mature Project*</label>";
											break;
										case "Beta":
											echo "<label class='label label-warning'>Beta Project</label>";
											break;
										case "Alpha":
											echo "<label class='label label-danger'>Alpha Project</label>";
											break;
										case "Inactive":
											echo "<label class='label label-default'>Inactive Project</label>";
											break;
										case "Release":
											echo "<label class='label label-info'>Release Project</label>";
											break;
									}
								?>
							</h4>
							<p><?=$output[$i]["description"];?></p>
							
							<div class="row">
								<div class="col-sm-4 col-lg-4">
									<select name="server" class="form-control">
										 <?php
										 	//get servers from db
										 	$data = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?");
										 	$data->bindParam(1,$_SESSION['userToken']);
										 	$data->execute();
										 	while($row = $data->fetch()){
											 	?>
											 		<option value="<?=$row['uuid'];?>"><?=$row['serverName'];?> - <?=$row['serverVersion'];?></option>
											 	<?php
										 	}
										 ?>
									</select>
								</div>
								<div class="col-sm-4 col-lg-4">
									<select name="version" class="form-control">
										<?php
											for($x = 0; $x < count($output[$i]['versions']);) {
												?>
													<option value="<?=$output[$i]['versions'][$x]['game_versions'][0];?>"><?=$output[$i]['versions'][$x]['game_versions'][0];?></option>
												<?php
												$x++;
											} //end for
										?>
									</select>
								</div>
								<div class="col-sm-4 col-lg-4">
									<button class="btn btn-primary btn-embossed btn-block">Add to server</button>
								</div>
							</div>
						</div>
					<?php
				}
				$i++;
			}
		} //end of searchPluginDB function
		
		public function searchPluginCatergory($category, $page) {
			$start = ($page -1) * 10;
			$ch = curl_init(); //setup curl object
			curl_setopt($ch, CURLOPT_URL, "http://api.bukget.org/3/categories/$category?start=$start&size=10&fields=slug,plugin_name,versions,stage,description"); //get request to bukget with $slug attached
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch); //execute curl
			if(curl_errno($ch)) {
				echo "Curl Error: " . curl_error($ch);
			}
			$output = json_decode($output, true);
			curl_close($ch); //close curl connection
			
			for($i = 0; $i < count($output);) {
				if($output[$i]["stage"] != "Deleted") {
					?>
						<div class="well">
							<!-- plugin here -->
							<h4><a href="/plugin&slug=<?=$output[$i]["slug"];?>"><?=$output[$i]["plugin_name"];?></a> - 
								<?php
									switch($output[$i]["stage"]) {
										case "Mature":
											echo "<label class='label label-success'>Mature Project*</label>";
											break;
										case "Beta":
											echo "<label class='label label-warning'>Beta Project</label>";
											break;
										case "Alpha":
											echo "<label class='label label-danger'>Alpha Project</label>";
											break;
										case "Inactive":
											echo "<label class='label label-default'>Inactive Project</label>";
											break;
										case "Release":
											echo "<label class='label label-info'>Release Project</label>";
											break;
									}
								?>
							</h4>
							<p><?=$output[$i]["description"];?></p>
							
							<div class="row">
								<div class="col-sm-4 col-lg-4">
									<select name="server" class="form-control">
										 <?php
										 	//get servers from db
										 	$data = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?");
										 	$data->bindParam(1,$_SESSION['userToken']);
										 	$data->execute();
										 	while($row = $data->fetch()){
											 	?>
											 		<option value="<?=$row['uuid'];?>"><?=$row['serverName'];?> - <?=$row['serverVersion'];?></option>
											 	<?php
										 	}
										 ?>
									</select>
								</div>
								<div class="col-sm-4 col-lg-4">
									<select name="version" class="form-control">
										<?php
											for($x = 0; $x < count($output[$i]['versions']);) {
												?>
													<option value="<?=$output[$i]['versions'][$x]['game_versions'][0];?>"><?=$output[$i]['versions'][$x]['game_versions'][0];?></option>
												<?php
												$x++;
											} //end for
										?>
									</select>
								</div>
								<div class="col-sm-4 col-lg-4">
									<button class="btn btn-primary btn-embossed btn-block">Add to server</button>
								</div>
							</div>
						</div>
				<?php
				}
				$i++;
			} //end for			
				?>
				<div class="row">
					<div class="col-sm-12 col-lg-12" id="pagination">
					<!-- pagination -->
							<?php
								if($page == "1"){
									?>
									<p>
										<a class="btn btn-primary" href="#" data-cat="<?=$category;?>" data-page="<?=$page + 1;?>">Next Page</a>
									</p>
									<?php
								} else {
									?>
									<p>
										<a class="btn btn-primary" href="#" data-cat="<?=$category;?>" data-page="<?=$page - 1;?>">Previous Page</a>
										<a class="btn btn-primary" href="#" data-cat="<?=$category;?>" data-page="<?=$page + 1;?>">Next Page</a>
									</p>
									<?php
								}
							?>
					</div>
				</div>
				
				<script>
					$("#pagination a").click(function(e){
						$.post('/php/plugins/ajaxSearch/catergorySearch.php', {cat:$(this).data("cat"), page:$(this).data("page")}, function(data){
							$("#returnData").html(data).fadeIn(1200);
						});
					});
				</script>
				<?php
		} //end of searchPluginCatergory
		
		public function pluginInfo($slug) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.bukget.org/3/plugins/bukkit/$slug");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch); //execute get request
			if(curl_errno($ch)){
				echo "Curl Error: " . curl_error($ch);
			} //curl error has occurred
			$output = json_decode($output, true); //json decode
			?>
				<div class="col-lg-7 col-sm-7">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">
								<?=$output["plugin_name"];?> - <label class="label label-success">CraftBukkit/Spigot*</label>
							</h3>
						</div>
						<div class="box-body">
							<img src="<?=$output["logo_full"];?>" />
						</div>
						<div class="box-body">
							<h5>Author: <p style="display: inline;"><?=$output["authors"][0];?></p></h5>
							<h5>Description:</h5>
								<?=$output["description"];?>
						</div>
						<div class="box-footer">
					
						</div>
					</div>
				</div>
				
				<div class="col-lg-5 col-sm-5">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Install Plugin</h3>
						</div>
						<div class="box-body">
							<form action="/php/plugins/installPlugin/installPlugin.php" method="POST" role="form" id="pluginForm">
							<div class="row">
								<div class="col-sm-6 col-lg-6">
									<h4>Server to install on</h4>
									<select class="form-control" name="clientServer" id="clientServer">
										<?php
											$query = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?");
											$query->bindParam(1, $_SESSION["userToken"]);
											$query->execute(); //execute query
											while($row = $query->fetch()) {
												?>
													<option value="<?=$row["uuid"];?>"><?=$row["serverName"];?> - Version: <?=$row["serverVersion"];?></option>
												<?php
											}
										?>
									</select>
								</div>
								
								<div class="col-sm-6 col-lg-6">
									<h4>Version to install</h4>
									<select class="form-control" name="pluginVersion" id="pluginversion">
										<?php
											for($i = 0; $i < count($output["versions"]);) {
												?>
													<option value="<?=$output["versions"][$i]["version"];?>"><?=$output["versions"][$i]["game_versions"][0];?></option>
												<?php
												$i++;
											} //end for
										?>
									</select>
								</div>
							</div>
								<p><input type="hidden" name="slug" value="<?=$slug;?>" id="slug" /></p>
							<div class="row">
								<div class="col-sm-12 col-lg-12">
									<button type="submit" id="pluginGO" class="btn btn-primary btn-block btn-embossed">INSTALL!</button>
								</div>
							</div>
							</form>
						</div>
						<div class="footer">
						
						</div>
					</div>
				</div>
			<?php
		} //end of pluginInfo function
		
		public function getServerStatusById($server) {
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE uuid = ? and owner = ?");
			$st->bindParam(1, $server);
			$st->bindParam(2, $this->owner);
			$st->execute();
			$row = $st->fetch();
			return $row;
		} //end of getServerStatusById function
		
		public function serverInfo($server) {
			$token = sha1(mt_rand(1,20).'aBcDeFgHiJkLnMoPqRsTuV');
			$_SESSION['formToken'] = $token;
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE uuid = ? and owner = ?");
			$st->bindParam(1, $server);
			$st->bindParam(2, $this->owner);
			$st->execute();
			while($row = $st->fetch()) {
				?>
					<div class="col-lg-7 col-sm-7">
						<div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><?=$row["serverName"];?></h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
										<div class="col-lg-8 col-sm-8">
											<p id="status">
												Status:
												<?php
													$query = $this->serverStatus($row['serverHost'], $row['serverVersion'], $row['serverPort']); //load server status
													if($query) {
														echo '<span class="label label-success">Online</span>&nbsp;'; //server online
														echo '<span class="label label-warning" id="players">' . $query['players'] . '/' . $query['maxplayers'] .  '</span>';
													}else {
														echo '<span class="label label-danger">Offline</span>'; //server offline
													} //end if|else
												?> 
											</p>
											<p>
												Version: <span class="label label-info label-xl"><?=$row["serverVersion"];?></span> &nbsp;
												Running: <span class="label label-default"><?=$row["serverVendor"];?></span>
											</p>
										</div>
										<div class="col-lg-4 col-sm-4 pull-right">
											<form action="/php/server/controller/controller.php" method="POST" id="serverControls">
												<input type="hidden" name="token" value="<?=$token;?>">
												<input type="hidden" name="serverid" value="<?=$server;?>">
												<input type="submit" name="start" value="Start" class="btn btn-success" id="start">
												<input type="submit" name="stop" value="Stop" class="btn btn-danger" id="stop">
											</form>
										</div>
                                    </div>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    
                                </div><!-- /.box-footer-->
                            </div>
                         <div class="box">
                         	<div class="box-header">
                         		<h3 class="box-title">Server Operators</h3>
                         		<div class="box-tools pull-right">
                         			<button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title"Collapse"><i class="fa fa-minus"></i></button>
                         		</div>
                         	</div>
                         	<div class="box-body">
                         		<table class="table table-hover">
                         			<thead>
                         				<tr>
                         					<th>In Game Name</th>
                         					<th></th>
                         				</tr>
                         			</thead>
                         			<tbody id="ops">
                         				
                         			</tbody>
                         		</table>
                         	</div>
                         	<div class="box-footer">
                         		
                         	</div>
                         </div>
                         
                         <div class="box">
                         	<div class="box-header">
                         		<h3 class="box-title">Server Plugins</h3>
                         		<div class="box-tools pull-right">
                         			<button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title"Collapse"><i class="fa fa-minus"></i></button>
                         		</div>
                         	</div>
                         	<div class="box-body">
                         		<table class="table table-hover">
                         			<thead>
                         				<tr>
                         					<th>Plugin Name</th>
                         					<th></th>
                         				</tr>
                         			</thead>
                         			<tbody id="plugins">
                         				
                         			</tbody>
                         		</table>
                         	</div>
                         	<div class="box-footer">
                         		
                         	</div>
                         </div>
					</div><!-- end col-lg-7 col-sm-7 -->
					
					<div class="col-lg-5 col-sm-5">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">Server Backups</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title"Collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="box-body">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>
												#
											</th>
											<th>
												Backup Name
											</th>
											<th>
												<div class="pull-right">
													<form action="/php/server/backups/createBackup.php" method="POST" id="createBackup">
														<input type="hidden" id="serverid" name="serverid" value="<?=$server;?>" />
														<input type="submit" id="crBK" value="+" class="btn btn-success" />
													</form>
												</div>
											</th>
										</tr>
									</thead>
									<tbody id="backups">
										
									</tbody>
								</table>
							</div>
						</div>
						
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">Console</h3>
								<div class="box-tools pull-right">
                         			<button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title"Collapse"><i class="fa fa-minus"></i></button>
                         		</div>
							</div>
							<div class="box-body">
								<div id="console">
									
								</div>
							</div>
							<div class="box-footer">
								
							</div>
						</div>
					</div><!-- end col-lg-5 col-sm-5 -->
				<?php
			}
		} //end of serverInfo function
		
		public function dashboardCounters() {
			$sql = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE owner = ?");
			$sql->bindParam(1, $_SESSION["userToken"]);
			$sql->execute(); //execute query
			$count = $sql->rowCount(); //count rows
			
			$sql2 = $this->datab->prepare("SELECT * FROM cpanel_hosts WHERE node_owner = ?");
			$sql2->bindParam(1, $_SESSION["userToken"]);
			$sql2->execute(); //execute
			$count2 = $sql2->rowCount(); 
			
			$sql3 = $this->datab->prepare("SELECT * FROM cpanel_jarRepo");
			$sql3->execute();
			$count3 = $sql3->rowCount();
			
			?>
				<div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                       	<?=$count;?>
                                    </h3>
                                    <p>
                                        Server(s) deployed
                                    </p>
                                </div>
                                <a href="#" class="small-box-footer">
                                   
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                        <?=$count2;?>
                                    </h3>
                                    <p>
                                       	Host(s) usable
                                    </p>
                                </div>
                                <a href="#" class="small-box-footer">
                                   
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        18,565
                                    </h3>
                                    <p>
                                        Plugins available
                                    </p>
                                </div>
                                <a href="#" class="small-box-footer">
                                  
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                    	<?=$count3;?>
                                    </h3>
                                    <p>
                                    	Alternative server softwares available
                                    </p>
                                </div>
                                <a href="#" class="small-box-footer">
                                
                                </a>
                            </div>
                        </div><!-- ./col -->
			<?php
		} //end of dashboardPage function
		
		public function getAccountSettings($username) {
			$st = $this->datab->prepare("SELECT * FROM cpanel_users WHERE username = ?");
			$st->bindParam(1, $username); //bind param to ?
			$st->execute(); //exeucte sql query
			while($row = $st->fetch()) {
				?>
					<div class="col-lg-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">
									Account settings
								</h3>
							</div>
							<div class="box-body">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Name</th>
											<th>Login Name</th>
											<th>Minecraft Account Name</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?=$row["first_name"] . " " . $row["last_name"];?></td>
											<td><?=$row["username"];?></td>
											<td>
												<?php
													if($row["minecraft_account_name"] == "") {
														?>
															No account linked!
														<?php
													} else {
														echo $row["minecraft_account_name"];
													}
												?>
											</td>
											<td>
												<a href="#" data-toggle="modal" data-target="#myModal" class="btn btn-warning">Change account password</a>
												<?php
													if($row["minecraft_account_name"] == "") {
														?>
															<a href="#" class="btn btn-success">Link Minecraft Account</a>
														<?php
													} else {
														?>
															<a href="#" class="btn btn-danger">Unlink Minecraft Account</a>
														<?php
													}
												?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="box-footer">
					
							</div>
						</div>
					</div>
				<?php
			}
		} //end getAccountSettings function
		
		public function updateUserPassword($oldPass, $newPass) {
			$st = $this->datab->prepare("SELECT * FROM cpanel_users WHERE username = ?");
			$st->bindParam(1, $_SESSION["userToken"]);
			$st->execute();
			$row = $st->fetch();
			
			$oldPassDB = $this->decryptData(base64_decode($row["password"]));
			if($oldPass == $oldPassDB) {
				$newPassDB = base64_encode($this->encryptData($newPass));
				$sta = $this->datab->prepare("UPDATE cpanel_users SET password = ? WHERE id = ?");
				$sta->bindParam(1, $newPassDB);
				$sta->bindParam(2, $row["id"]);
				$sta->execute();
				echo "Password has been updated successfully!";
			} else {
				die("Old password did not match our records");
			}
		} //end updateUserPassword function

	} //end of ControlPanel class
?>