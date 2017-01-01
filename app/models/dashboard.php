<?php
class dashboardModel
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getDashboardCounters($jwt)
    {
        $id = $jwt->id;

        $sql = $this->db->prepare("SELECT
        (SELECT COUNT(DISTINCT serverId) FROM servers WHERE ownerId = :search) AS Servers,
        (SELECT COUNT(DISTINCT hostId) FROM hosts WHERE ownerId = :search) AS Hosts,
        (SELECT COUNT(DISTINCT jarId) FROM jarrepo WHERE ownerId = :search) AS Jars;");
        $sql->bindParam(':search', $id);
        $sql->execute();

        $data = $sql->fetch();
        echo json_encode($data);
    }
}
