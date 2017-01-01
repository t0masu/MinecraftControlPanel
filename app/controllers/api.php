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
                default:

                    break;
            }
        }
    }
}
