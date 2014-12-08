============================================
PHP DOCUMENTATION FOR CONTROL PANEL(t0masu):
============================================

===========
Main Class:
===========

The variable $ControlPanel is the instantiated main class, most/the majority functions are called from it.
			 
To create a new script with the class instantiated 
under the given variable include the settings.php 
file under "PHP/INCLUDES/settings.php"
		 
===============================================
FUNCTIONS(main.php listed in appearance order):
===============================================

	=============================
	MAIN CONSTRUCTOR __construct:
	=============================
	===>The public variable "$isConnected" is set to true or false dependent on 
	====>connection status in try/catch
	===>try/catch for connection to database specified in php/includes/settings.php
	
	***********
	ATTRIBUTES:
	===>PDO::ATTR_ERRMODE
	===>PDO::ERRMODE_EXCEPTION
	===>PDO::ATTR_DEFAULT_FETCH_MODE
	===>PDO::FETCH_ASSOC
	***********
	
	CONSTRUCTOR FUNCTION:
	Instantiate the database object and connect to the database for interaction with the functions
	=============================

	========================
	PUBLIC FUNCTION closeDB:
	========================
	===>The Variable "datab" is set to null
	===>The Variable "isConnected" is set to false
	
	FUNCTION PURPOSE:
	Close unecessary connectivity to database freeing memory
	========================
	
	===============================
	PRIVATE FUNCTION serverConnect:
	===============================
	**********
	ARGUMENTS:
	===>Host(as $host) is the ip/hostname we are connecting to
	===>Port(as $port) is the port we are connecting to
	**********
	
	FUNCTION PURPOSE:
	Create socket and connect to target server on given port
	===============================
	
	==================================
	PRIVATE FUNCTION serverDisconnect:
	==================================
	ARGUMENTS:
	===>Socket(as $socket) is the given socket we have connected to
	
	FUNCTION PURPOSE:
	Disconnect from the target and close the socket
	==================================
	
	==================================
	PRIVATE FUNCTION readPacketLength:
	==================================
	FUNCTION PURPOSE:
	Read the packet length of the answer from target server on the 1.7+ protocol
	==================================
	
	=============================
	PUBLIC FUNCTION serverStatus:
	=============================
	*******************
	FUNCTION ARGUMENTS:
		The Argument $host is the host server ip/hostname for the scan
		The Argument $version is the minecraft server version used for the scan, due to protocol change
		1.7/1.8 are scanned differently as the protocol isn't the same, more data is returned in effect
		The Argument $port is the port the scanner is using to connect to, default is $port = "25565"
	
	*************
	ARRAY VALUES:
		VAR IS $ServerData (key is case-sensitive btw)

	"hostname" 		=> 	$host,
	"version" 		=>	false,
	"protocol"		=>	false,
	"players"		=>	false,
	"maxplayers"		=>	false,
	"motd"			=>	false,
	"motd_raw"		=>	false,
	"favicon"		=>	false,
	"ping"			=>	false,
	
	************
	OUTPUT DATA:
	The output of the function is a json decoded(if newer protocol 1.7+) data set of information entailing about the given server,
	it's returned as the $ServerData array
	
	******
	USAGE:
	Call function to var 
		$query = $ControlPanel->serverStatus(host, version, port);
	The data is now contained within the chosen variable 
	Use the array values listed above to get to desired data 
	
	*******************
	FUNCTION PURPOSE:
	To get the online status of a target minecraft server and return true or false and data on true.
	
	=============================
	
	====================================
	PUBLIC FUNCTION UUIDTokenComparison:
	====================================
	
	*******************
	FUNCTION ARGUMENTS:
	The Argument $uuid is the server uuid in the database needed to located the correct data
	The Argument $owner is referenced in the constructor as $_SESSION["userToken"] and inherits
	as &$owner in the function
	
	FUNCTION PURPOSE:
	Checks the database for a server with the user
	a) Being the registered owner b) The Servers UUID being the same as sent
	
	====================================

	===================================
	PUBLIC FUNCTION generateServerList:
	===================================
	
	*******************
	FUNCTION ARGUMENTS:
	The Argument &$owner is the only argument for the function, it supplys the user auth token to identify
	the servers owned by the user.
	
	FUNCTION PURPOSE:
	Generates a list of servers owned by the user with the correct format for the Control Panel 
	template and renders it accordingly.
	===================================
	
	=====================================
	PUBLIC FUNCTION setupSFTPEnvironment:
		
	*******************	
	FUNCTION ARGUMENTS:
		The $host variable is the remote servers ip/hostname
		The $username variable is the username required to authenticate with the remote server
		The $password variable is the password reuiqred to authentice with the remote server
		The $port variable is the port required for the ssh client to connect to
		
	FUNCTION PURPOSE:
		Setup the SFTP Environment on the users remote server for several functions dependent on it
	=====================================
	
	==========================================
	PUBLIC FUNCTION sendCommandtoRemoteServer:
	==========================================
	
	*******************
	FUNCTION ARGUMENTS:
		The argument $host is the remote servers ip/hostname
		The argument $username is the username required to authenticate
		The argument $password is the password required to authenticate
		The argument $port is the port the remote server is listening on for the ssh connection
		The argument $command is the command being send over ssh to the remote server
	
	FUNCTION PURPOSE:
		Sends a chosen command to remote server
	==========================================
	
	=====================================
	PUBLIC FUNCTION getServerPluginsList:
	=====================================
	
	*******************
	FUNCTION ARGUMENTS:
	The Argument $uuid is the server needed to identify plugins on the remote server
	
	FUNCTION PURPOSE:
	Get a list of plugins on remote server over ssh
	=====================================
	
	======================================
	PUBLIC FUNCTION addRemoteServerPlugin:
	======================================
	
	FUNCTION PURPOSE:
	======================================
	
	=========================================
	PUBLIC FUNCTION removeRemoteServerPlugin:
	=========================================
	
	FUNCTION PURPOSE:
	=========================================
	
	=====================================
	PUBLIC FUNCTION getServerBackupsList:
	=====================================
	
	FUNCTION PURPOSE:
	=====================================

	=========================================
	PUBLIC FUNCTION createRemoteServerBackup:
	=========================================
	
	FUNCTION PURPOSE:
	=========================================
	
	===================================
	PUBLIC FUNCTION getServerOperators:
	===================================
	
	FUNCTION PURPOSE:
	===================================
	
	=====================================
	PUBLIC FUNCTION addSelfManagedServer:
	=====================================
	
	FUNCTION PURPOSE:
	=====================================
	
	===================================
	PUBLIC FUNCTION createRemoteServer:
	===================================
	
	FUNCTION PURPOSE:
	===================================
		
	==================================
	PUBLIC FUNCTION startRemoteServer:
	==================================
	
	FUNCTION PURPOSE:
	==================================
	
	=================================
	PUBLIC FUNCTION stopRemoteServer:
	=================================
	
	FUNCTION PURPOSE:
	=================================
	
========================================
VARIABLES(PRIVATE,PUBLIC,PROTECTED etc):
========================================

	======================
	SESSION['userToken']:
		This the main php user authentication token, access via $_SESSION['userToken']
	======================
	
	==================
	PROTECETED $datab:
		This var is the PDO MySQL Object 
	==================
	
	====================
	PUBLIC $isConnected:
		This var holds the connection status for the PDO Object
	====================
	
	==============
	PUBLIC $owner:
		This var holds the owner of the given server several functions are dependent on this
	==============