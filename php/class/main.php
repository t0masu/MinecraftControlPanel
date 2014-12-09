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

		//Start Minecraft Server Status functions
		
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
		
		public function serverData($uuid) {
			
		} //end of serverData function
		
		public function setupSFTPEnvironment($host, $username, $password, $port) {
			try {
				$connection = ssh2_connect($host, $port); //connect to the remote server
				ssh2_auth_password($connection, $username, $password); //authenticate with the remote server
				$sftp = ssh2_sftp($connection); //initalise the sftp environment
			}catch (Exception $e){
				throw new Exception("Fail to connect");
			}
			return $sftp; //return the final sftp environment
		} //end of setupSFTPEnvironment
		
		public function sendCommandtoRemoteServer($host, $username, $password, $port, $command){
			$connection = ssh2_connect($host, $port);
			ssh2_auth_password($connection, $username, $password);
			$command = ssh2_exec($connection, $command);
			return $command;
		} //end of sendCommandtoRemoteServer
		
		public function getServerPluginsList($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid); //get comparison and get data array
			if($uuidTokenComparison[2] == 1){
				$row = $uuidTokenComparison[2]->fetch(); //fetch rows
				$sftp = $this->setupSFTPEnvironment($row['sshHost'], $row['sshUser'], $row['sshPass'], $row['sshPort']); //setup sftp environment
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
				for($i = 0; $i < count($jars);){
					$e = $i + 1; //fixes the id starting on number 0
						//code here for table, list or what ever
					$i++;
				} //end for loop
				
			}else {
				return false; //fails the comparison
			}
		} //end of getServerPluginsList function
		
		public function addRemoteServerPlugin(){} //end of addRemoteServerPlugin function
		
		public function removeRemoteServerPlugin(){} //end of removeRemoteServerPlugin function
		
		public function getServerBackupsList($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid); //get comparison and get data array
			if($uuidTokenComparison[2] == 1){ //check the validity of the request
				$row = $uuidTokenComparison[2]->fetch(); //fetch rows from the database
				$sftp = $this->setupSFTPEnvironment($row['sshHost'], $row['sshUser'], $row['sshPass'], $row['sshPort']); //setup sftp environment
				if(substr($row['serverPath'], -1) == "/"){
					$path = $path . "backups/"; // add / to the directory loaded from the database
				}else {
					$path = $path . "/backups/"; // add / and / to the directory loaded from the database
				}
				$directoryHandle = opendir("ssh2.sftp://$sftp/$path");
				while(false !==($file = readdir($directoryHandle))){
					if($file != "." && $file != ".."){ 
						if(substr($file, -4) == ".zip" || ".tar"){
							$backups[1][] = $file; //filename only
							$backups[2][] = $path.$file; //end result of the zip and tar file check 
							foreach($backups[2] as $filename){
								$data[] = ssh2_sftp_stat($sftp, $filename); //data returned about the file on remote server
							}
						}
					}
				}//end while loop
				for($i = 0; $i < count($backups[2]);){
					$e = $i + 1; //fixes the start on 0 issue
					//styled return here
					$i++;
				}
			}else {
				return false; //fails to comply with the check
			}
		}// end of getServerBackupsList function
		
		public function createRemoteServerBackup($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid);
			if($uuidTokenComparison[2] == 1){
				$row = $uuidTokenComparison[1]->fetch(); //fetch all releveant rows from the database
				$sftp = ssh2_sftp($row['sshHost'], $row['sshUser'], $row['sshPass'] , $row['sshPort']); //setup sftp environment
				$path = $row['serverPath'];
				$bkpath = $row['bkpath'];
				if(substr($path, -1) && substr($bkpath, -1) != "/"){
					$bkpath = $bkpath . "/";
					$path = $path . "/";
				}
				
				if(preg_match($path, $bkpath)){
					$command = "tar -cvf --exclude=" . basename($bkpath) . "* " . $bkpath . "backup" . date("Y-m-d H:i:s") . " " . $path . "*";
					$command = ssh2_exec($connection, $command);
					if($command){
						return true;
					}else {
						return false;
					}
				}else {
					$command = "tar -cvf " . $bkpath . "backup" . date("Y-m-d H:i:s") . " " . $path . "*";
					$command = ssh2_exec($connection, $command);
					if($command){
						return true;
					}else {
						return false;
					}
				}
			}
		} //end of createRemoteServerBackup function
		
		public function getServerOperators(){} //end of getServerOperators function
		
		public function addSelfManagedServer(){} //end of addSelfManagedServer function
		
		public function createRemoteServer(){} //end of createRemoteServer function
		
		public function startRemoteServer($uuid){
			$uuidTokenComparison = $this->UUIDTokenComparison($uuid);
			if($uuidTokenComparison[2] == 1){
				$row = $uuidTokenComparison[1]->fetch();
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
			if($uuidTokenComparison[2] == 1){
				$row = $uuidTokenComparison[1]->fetch();
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
			$sql = $this->datab->prepare("SELECT * FROM cpanel_users WHERE username = ? and password = ?");
			$sql->bindParam(1, $username);
			$sql->bindParam(2, $password);
			$sql->execute();
			$count = $sql->rowCount();
			return $count;
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
			$i = 0;
			while($i < count($output)) {
				?>
					<div class="jumbotron">
							<!-- plugin here -->
							<h3><?=$output[$i]["plugin_name"];?></h3>
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
											$versions = count($output[$i]["versions"][0]["game_versions"]);
											$x = 0;
											while($x < $versions){
												?>
													<option value="<?=$output[$i]['versions'][0]['game_versions'][$x];?>"><?=$output[$i]['versions'][0]['game_versions'][$x];?></option>
												<?php
												$x++;
											}
										?>
									</select>
								</div>
								<div class="col-sm-4 col-lg-4">
									<button class="btn btn-primary btn-embossed btn-block">Add to server</button>
								</div>
							</div>
						</div>
				<?php
					$i++;
				}
			curl_close($ch);
		} //end of searchPluginDB function
		
		public function serverInfo($server) {
			$st = $this->datab->prepare("SELECT * FROM cpanel_servers WHERE uuid = ? and owner = ?");
			$st->bindParam(1, $server);
			$st->bindParam(2, $this->owner);
			$st->execute();
			while($row = $st->fetch()) {
				?>
					<div class="jumbotron">
						<h3><?=$row['serverName'];?></h3>
						
						<p>
						
						<span class="label label-primary">
							Version: MC <?=$row['serverVersion'];?>
						</span>
							&nbsp;
						<span class="label label-primary">
							Status: <?php
										$query = $status = $this->serverStatus($row['serverHost'], $row['serverVersion'], $row['serverPort']);
										if($query){
											?>
												<span class="label label-success">
													Online
												</span>
													&nbsp;
												<span class="label label-warning">
													<?=$query['players'] . "/" . $query['maxplayers'];?>
												</span>
											<?php
										}else {
											?>
												<span class="label label-danger">
													Offline
												</span>
											<?php
										}
									?>
						</span>
						</p>
					</div>
				<?php
			}
		} //end of serverInfo function
		
	} //end of class
?>