<?php
class hostsModel
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserHosts($jwt)
    {
        $id = $jwt->id;

        $sql = $this->db->prepare("SELECT * FROM hosts WHERE ownerId = ?");
        $sql->bindParam(1, $id);
        $sql->execute();
        $data = $sql->fetchAll();

        echo json_encode($data);
    }

    public function createUserHost($jwt)
    {
        //fetch form inputs
        $data = json_decode(file_get_contents("php://input"), 1);

        //setup query inputs
        $ownerId = $jwt->id;
        $hostName = $data['hostName'];
        $hostAddress = $data['hostAddress'];
        $hostProtocol = $data['hostProtocol'];
        $hostUsername = $data['hostUsername'];
        $hostPassword = $data['hostPassword'];

        if(isset($data['hostPort']))
        {
            $hostPort = $data['hostPort'];
        }
        else
        {
            if($hostProtocol == 'SSH')
            {
                $hostPort = 22;
            }
            else
            {
                $hostPort = 23;
            }
        }

        $sql = $this->db->prepare("INSERT INTO hosts (ownerId, hostName, hostAddress, hostProtocol, hostUsername, hostPassword, hostPort) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $sql->bindParam(1, $ownerId);
        $sql->bindParam(2, $hostName);
        $sql->bindParam(3, $hostAddress);
        $sql->bindParam(4, $hostProtocol);
        $sql->bindParam(5, $hostUsername);
        $sql->bindParam(6, $hostPassword);
        $sql->bindParam(7, $hostPort);

        $exec = $sql->execute();
        if($exec)
        {
            echo json_encode((object) array('success'=>true));
        }
        else
        {
            echo json_encode((object) array('error'=>true));
        }

    }

    public function getHostById($input)
    {
        $id = $input['hostId'];
        $sql = $this->db->prepare("SELECT * FROM hosts WHERE hostId = ?");
        $sql->bindParam(1, $id);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
}
