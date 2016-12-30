<?php
class auth extends Controller
{
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), 1);
        $this->userModel->parseLogin($data);
    }

    public function generatePasswordHash()
    {
        echo password_hash('pass123', PASSWORD_DEFAULT);
    }
}
