<?php
class serversModel
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserServers($jwt)
    {
        $id = $jwt->id;

        $sql = $this->db->prepare("SELECT * FROM servers WHERE ownerId = ?");
        $sql->bindParam(1, $id);
        $sql->execute();
        $data = $sql->fetchAll();

        echo json_encode($data);
    }

    private function guid()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function getAddress($jwt, $id)
    {
        $ownerId = $jwt->id;
        $sql = $this->db->prepare("SELECT * FROM servers WHERE ownerId = ? and serverId = ?");
        $sql->bindParam(1, $ownerId);
        $sql->bindParam(2, $id);
        $sql->execute();

        $data = $sql->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function getMinecraftUUID($username)
    {
        if(!empty($username))
        {
            // $username = filter_var($username, FILTER_SANITIZE_STRING);
            $curl = curl_init("https://api.mojang.com/users/profiles/minecraft/" . $username);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $uuid = curl_exec($curl);
            curl_close($curl);

            $uuid = json_decode($uuid);

            $output = [];
            $object = new stdClass();

            $object->id = $uuid->id;
            $object->name = $uuid->name;
            $object->level = 4;
            $object->bypassPlayerLimit = false;

            $output[] = $object;
            $output = json_encode($output);

            return $output;
        }
    }

    public function createServer($jwt, $minecraftServerData, $hostData)
    {
        if(!empty($minecraftServerData))
        {
            $id = $jwt->id; //setup ownerId
            $uid = $this->guid();
            $serverName = "Autocreated Server " . $uid;
            $serverPath = "/home/" . $hostData['hostUsername'] . "/cpanel_servers" . "/" . $id . '/' . $uid . '/'; //path for server on host
            $serverPort = 25565;
            $serverHost = $hostData['hostAddress'];
            $serverVersion = $minecraftServerData['id'];
            $sshUser = $hostData['hostUsername'];
            $sshPassword = $hostData['hostPassword'];
            $sshPort = $hostData['hostPort'];
            $ramSize = 1024;
            $jarName = "server.jar";
            $defaultState = 0;

            $tempFile = tmpfile();
            $eula = tmpfile();
            $ops = tmpfile();

            if($jwt->minecraftUsername != null)
            {
                $opList = $this->getMinecraftUUID($jwt->minecraftUsername);
            }
            else
            {
                $opList = json_encode(array(), 1);
            }


            //fill bash script with vars
            ob_start();
            require(APP . 'core/bash/script.txt');
            $bashScript = ob_get_contents();
            ob_end_clean();
            $bashTemp = tmpfile();
            fwrite($bashTemp, $bashScript);

            fwrite($eula, 'eula=true');
            fwrite($ops, $opList); //set setup account as op

            $downloadLink = $minecraftServerData['downloads']['server']['url'];
            $curl = curl_init($downloadLink);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //prevents file not being downloaded

            $file = curl_exec($curl);
            fwrite($tempFile, $file);
            curl_close($curl);

            // cmds
            if($tempFile)
            {
                $session = ssh2_connect($serverHost, $sshPort);
                ssh2_auth_password($session, $sshUser, $sshPassword);
                // mkdir in home directory
                ssh2_exec($session, "mkdir -p $serverPath");
                ssh2_scp_send($session, stream_get_meta_data($tempFile)['uri'], $serverPath . "/server.jar");
                ssh2_scp_send($session, stream_get_meta_data($eula)['uri'], $serverPath . "/eula.txt");
                ssh2_scp_send($session, stream_get_meta_data($ops)['uri'], $serverPath . "/ops.json");
                ssh2_scp_send($session, stream_get_meta_data($bashTemp)['uri'], $serverPath . '/start.sh');

                ssh2_exec($session, 'chmod +x ' . $serverPath . '/start.sh');
                ssh2_exec($session, 'exit');

                fclose($tempFile);
                fclose($eula);
                fclose($ops);
                fclose($bashTemp);

                $sql = $this->db->prepare("INSERT INTO `servers` (ownerId, serverName, serverHost, serverPort, serverVersion, serverRAM, serverPath, sshUser, sshPass, sshPort, jarName) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
                $sql->bindParam(1,$id);
                $sql->bindParam(2,$serverName);
                $sql->bindParam(3,$serverHost);
                $sql->bindParam(4,$serverPort);
                $sql->bindParam(5,$serverVersion);
                $sql->bindParam(6, $ramSize);
                $sql->bindParam(7,$serverPath);
                $sql->bindParam(8,$sshUser);
                $sql->bindParam(9,$sshPassword);
                $sql->bindParam(10,$sshPort);
                $sql->bindParam(11,$jarName);

                $insert = $sql->execute();

                unset($session);

                if($insert)
                {
                    echo json_encode((object) array("success"=>true, "ops"=>$opList));
                }
            }
            else
            {
                echo json_encode((object) array("error"=>true));
            }
        }

    }

    public function getServerData($jwt, $input)
    {
        $serverId = $input['id'];
        $id = $jwt->id;
        $sql = $this->db->prepare("SELECT * FROM servers WHERE serverId = ? and ownerId = ?");
        $sql->bindParam(1, $serverId);
        $sql->bindParam(2, $id);
        $sql->execute();

        $data = $sql->fetch(PDO::FETCH_ASSOC);

        if($data['status'] == 0)
        {
            //server has not been run for the first time
            echo json_encode($data);
        }
        else
        {
            //connect to server and get ops, banned, backups, config etc.
            // $ssh = ssh2_connect($data['serverHost'], $data['sshPort']);
            // ssh2_auth_password($ssh, $data['sshUser'], $data['sshPass']);
            //
            // $path = $data['serverPath'];
            //
            // $ops = $path . '/ops.json';
            // $bannedPlayers = $path . '/banned-players.json';
            // $properties = $path . '/server.properties';
            // $whitelist = $path . '/whitelist.json';
            //
            // //get files over scp
            // $sftp = ssh2_sftp($ssh);
            //
            // $output = [];
            // $opsStream = fopen("ssh2.sftp://$sftp$ops", "r");
            // $output['ops'] = stream_get_contents($opsStream);
            // fclose($opsStream);

            // $bannedPlayers = @fopen("ssh2.sftp://$ssh/$bannedPlayers", "r");
            // $output['bannedPlayers'] = stream_get_contents($bannedPlayers);
            // fclose($bannedPlayers);
            //
            // $propertiesStream = @fopen("ssh2.sftp://$ssh/$properties", "r");
            // $output['properties'] = stream_get_contents($propertiesStream);
            // fclose($propertiesStream);
            //
            // $whitelistStream = @fopen("ssh2.sftp://$ssh/$whitelist", "r");
            // $output['whitelist'] = stream_get_contents($whitelistStream);
            // fclose($whitelistStream);
            //
            // unset($sftp);
            // unset($ssh);
            //
            // $finalOutcome = array_merge($data, $output);

            echo json_encode($data);
        }

    }

    public function startServer($jwt, $serverId)
    {
        $id = $jwt->id;
        $sql = $this->db->prepare("SELECT * FROM servers WHERE ownerId = ? and serverId = ?");
        $sql->bindParam(1, $id);
        $sql->bindParam(2, $serverId);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_ASSOC);

        $ssh = ssh2_connect($data['serverHost'], $data['sshPort']);
        ssh2_auth_password($ssh, $data['sshUser'], $data['sshPass']);
        $command = $data['serverPath'] . '/start.sh start';
        $cmd = ssh2_exec($ssh, "cd " . $data['serverPath'] . ";" . $command);
        unset($ssh);

        $sql2 = $this->db->prepare("UPDATE servers SET status = '1' WHERE serverId = ? and ownerId = ?");
        $sql2->bindParam(1, $serverId);
        $sql2->bindParam(2, $id);
        $sql2->execute();
    }

    public function stopServer($jwt, $serverId)
    {
        $id = $jwt->id;
        $sql = $this->db->prepare("SELECT * FROM servers WHERE ownerId = ? and serverId = ?");
        $sql->bindParam(1, $id);
        $sql->bindParam(2, $serverId);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_ASSOC);

        $ssh = ssh2_connect($data['serverHost'], $data['sshPort']);
        ssh2_auth_password($ssh, $data['sshUser'], $data['sshPass']);
        $command = $data['serverPath'] . '/start.sh stop';
        $cmd = ssh2_exec($ssh, "cd " . $data['serverPath'] . ";" . $command);
        unset($ssh);
    }

    public function restartServer($jwt, $serverId)
    {
        $id = $jwt->id;
        $sql = $this->db->prepare("SELECT * FROM servers WHERE ownerId = ? and serverId = ?");
        $sql->bindParam(1, $id);
        $sql->bindParam(2, $serverId);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_ASSOC);

        $ssh = ssh2_connect($data['serverHost'], $data['sshPort']);
        ssh2_auth_password($ssh, $data['sshUser'], $data['sshPass']);
        $command = '/start.sh restart';
        $cmd = ssh2_exec($ssh, "cd " . $data['serverPath'] . ";" . $command);
        unset($ssh);
    }
}
