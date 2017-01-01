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

    public function createServer($jwt, $minecraftServerData, $hostData)
    {
        if(!empty($minecraftServerData))
        {
            $id = $jwt->id; //setup ownerId
            $serverName = "Autocreate Server " . $id;
            $serverPath = "/home/" . $hostData['hostUsername'] . "/" . $id; //path for server on host
            $serverPort = 25565;
            $serverHost = $hostData['hostAddress'];
            $serverVersion = $minecraftServerData['id'];
            $sshUser = $hostData['hostUsername'];
            $sshPassword = $hostData['hostPassword'];
            $sshPort = $hostData['hostPort'];
            $ramSize = 1024;
            $jarName = "server.jar";

            $tempFile = tmpfile();
            $downloadLink = $minecraftServerData['downloads']['server']['url'];
            $curl = curl_init($downloadLink);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

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
                ssh2_exec($session, 'exit');
                fclose($tempFile);

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
                    echo json_encode((object) array("success"=>true));
                }
            }
            else
            {
                echo json_encode((object) array("error"=>true));
            }
        }
        else
        {
            die();
        }
    }
}
