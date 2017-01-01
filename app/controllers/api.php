<?php
class Api extends Controller
{
    public function index()
    {
        echo 'minecraftControlPanel API endpoint';
    }

    public function dashboard($param)
    {
        $jwt = $this->verifyJWT();
        if($jwt)
        {
            switch($param)
            {
                case "counters":
                    $this->dashboardModel->getDashboardCounters($jwt);
                    break;
                default:

                    break;
            }
        }
    }

    public function hosts($param)
    {
        $jwt = $this->verifyJWT();
        if($jwt)
        {
            switch($param)
            {
                case "all":
                    $this->hostsModel->getUserHosts($jwt);
                    break;
                case "add":
                    $this->hostsModel->createUserHost($jwt);
                    break;
                default:

                    break;
            }
        }
    }

    public function servers($param)
    {
        $jwt = $this->verifyJWT();
        if($jwt)
        {
            switch($param)
            {
                case "all":
                    $this->serversModel->getUserServers($jwt);
                    break;
                case "onHost":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $hostData = $this->hostsModel->getHostById($input['host']);
                    $minecraftData = $this->minecraftModel->fetchDownloadURLS($input['minecraftVersion']);

                    $this->serversModel->createServer($jwt, $minecraftData, $hostData);
                    break;
                default:

                    break;
            }
        }
    }

    public function minecraft($param)
    {
        $jwt = $this->verifyJWT();
        if($jwt)
        {
            switch($param)
            {
                case "versions":
                    $this->minecraftModel->fetchMinecraftVersions();
                    break;
                    $this->minecraftModel->fetchMinecraftVersions();
                    break;
                case "":

                    break;
            }
        }
    }
}
