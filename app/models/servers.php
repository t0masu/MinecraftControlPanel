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
}
