<?php

class Controller
{
    /**
     * @var null Database Connection
     */
    public $db = null;

    /**
     * @var null Model
     */
    public $model = null;

    /**
     * Whenever controller is created, open a database connection too and load "the model".
     */
    function __construct()
    {
        $this->openDatabaseConnection();
        $this->loadModel();
    }

    /**
     * Open the database connection with the credentials from application/config/config.php
     */
    private function openDatabaseConnection()
    {
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
    }

    /**
     * Loads the "model".
     * @return object model
     */
    public function loadModel()
    {
        // User model for auth and userdata
        require_once APP. 'models/user.php';
        require_once APP. 'models/dashboard.php';
        require_once APP . 'models/hosts.php';
        require_once APP . 'models/servers.php';

        $this->userModel = new userModel($this->db);
        $this->dashboardModel = new dashboardModel($this->db);

        $this->hostsModel = new hostsModel($this->db);
        $this->serversModel = new serversModel($this->db);
    }

    public function verifyJWT()
    {
        $jwt = new JWT();
        $tokenData = $_SERVER['HTTP_X_ACCESS_TOKEN'];
        return $jwt->decode($tokenData, JWT_KEY);
    }
}
