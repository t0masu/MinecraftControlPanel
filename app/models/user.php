<?php
class userModel {
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function parseLogin($data)
    {
        $username = $data['username'];

        //find user
        $sql = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $sql->bindParam(1, $username);
        $sql->execute();
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        $counter = $sql->rowCount();

        //if user doesn't exist
        if($counter < 0) {
            $output = (object) array(
                "error" => true,
                "errorMessage" => "User doesn't exist"
            );
            echo json_encode($output);
        }
        else {
            //compare hashes
            $userId = $data['username'];
            $password = $data['password'];

            if(password_verify($password, $user['password']))
            {
                //password is valid regenerate
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql2 = $this->db->prepare("UPDATE users SET password = ? WHERE username = ?");
                $sql2->bindParam(1, $hash);
                $sql2->bindParam(2, $userId);
                $sql2->execute();

                //create jwt
                $payload = (object) array(
                    "user" => $user['username'],
                    "firstName" => $user['firstName'],
                    "lastName" => $user['lastName'],
                    "access" => $user['status']
                );

                require_once 'jwt.php';
                $jwt = new JWT();

                $token = $jwt->encode($payload, JWT_KEY);

                $output = (object) array(
                    "success" => true,
                    "jwt" => $token
                );
                echo json_encode($output);
            }
            else
            {
                //invalid password
                $output = (object) array(
                    "error" => true,
                    "errorMessage" => "Password is incorrect"
                );
                echo json_encode($output);
            }
        }
    } //end of parseLogin
}
