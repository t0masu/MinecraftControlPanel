<?php
class account {
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAccountData($jwt)
    {
        $id = $jwt->id;
        $sql = $this->db->prepare("SELECT firstName, lastName, username, minecraftUsername, status FROM users WHERE userId = ? LIMIT 1;");
        $sql->bindParam(1, $id);
        $sql->execute();
        $data = $sql->fetch();
        echo json_encode($data);
    }

    public function changePassword($jwt, $formData)
    {
        $id = $jwt->id;
        $currentPassword = $formData['currentPassword'];
        $newPassword = $formData['newPassword'];

        //check user passwords
        $sql = $this->db->prepare("SELECT * FROM users WHERE userId = ? LIMIT 1;");
        $sql->bindParam(1, $id);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_ASSOC);

        //confirm current password
        if(password_verify($currentPassword, $data['password']))
        {
            //update password
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql2 = $this->db->prepare("UPDATE users SET password = ? WHERE userId = ?");
            $sql2->bindParam(1, $hash);
            $sql2->bindParam(2, $id);
            $result = $sql2->execute();

            if($result)
            {
                echo json_encode((object) array('success'=>true));
            }
            else
            {
                echo json_encode((object) array('error'=>true));
            }
        }
        else
        {
            //no dice
            echo json_encode((object) array('error'=>true));
        }
    }

    public function linkAccount($jwt, $formData)
    {
        $id = $jwt->id;
        $minecraftUsername = $formData['accountName'];

        $sql = $this->db->prepare("UPDATE users SET minecraftUsername = ? WHERE userId = ?");
        $sql->bindParam(1, $minecraftUsername);
        $sql->bindParam(2, $id);
        $result = $sql->execute();

        if($result)
        {
            echo json_encode((object) array('success'=>true));
        }
        else
        {
            echo json_encode((object) array('error'=>true));
        }
    }

    public function unlinkAccount($jwt)
    {
        $id = $jwt->id;
        $sql  = $this->db->prepare("UPDATE users SET minecraftUsername = null WHERE userId = ?");
        $sql->bindParam(1, $id);
        $result = $sql->execute();

        if($result)
        {
            echo json_encode((object) array('success'=>true));
        }
        else
        {
            echo json_encode((object) array('error'=>true));
        }
    }
}
