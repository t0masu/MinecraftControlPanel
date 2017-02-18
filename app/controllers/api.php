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
                case "getServerData":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $this->serversModel->getServerData($jwt, $input);
                    break;
                case "startServer":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $this->serversModel->startServer($jwt, $input['id']);
                    break;
                case "rebootServer":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $this->serversModel->rebootServer($jwt, $input['id']);
                    break;
                case "stopServer":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $this->serversModel->stopServer($jwt, $input['id']);
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
                case "getStatus":
                    $input = json_decode(file_get_contents("php://input"), 1);
                    $serverData = $this->serversModel->getAddress($jwt, $input['id']);
                    echo $this->minecraftModel->getStatus($serverData['serverHost'], $serverData['serverPort']);
                    break;
            }
        }
    }

    public function account($param)
    {
        $jwt = $this->verifyJWT();
        if($jwt)
        {
            switch($param)
            {
                case "getAccountData":
                    $this->accountModel->getAccountData($jwt);
                    break;
                case "changePassword":
                    $formData = json_decode(file_get_contents("php://input"), 1);
                    $this->accountModel->changePassword($jwt, $formData);
                    break;
                case "linkAccount":
                    $formData = json_decode(file_get_contents("php://input"), 1);
                    $this->accountModel->linkAccount($jwt, $formData);
                    break;
                case "unlinkAccount":
                    $this->accountModel->unlinkAccount($jwt);
                    break;
            }
        }
    }

    public function infophp()
    {
        phpinfo();
    }

    // public function ssh()
    // {
    //     $con = ssh2_connect('172.16.0.104', 22);
    //     ssh2_auth_password($con, 'minecraft', 'minecraft');
    //
    //     $output = ssh2_exec($con, 'uptime');
    //     stream_set_blocking($output, true);
    //     $output = ssh2_fetch_stream($output, SSH2_STREAM_STDIO);
    //     $data = stream_get_contents($output);
    //
    //     $data = explode(',', $data);
    //     echo json_encode((object) $data);
    // }

    public function status()
    {
        echo json_encode($this->minecraftModel->getStatus('play.atlanticnetwork.org', 25565));
    }
}
