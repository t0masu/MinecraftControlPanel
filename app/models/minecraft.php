<?php
class minecraft
{
    public function __construct(){}

    public function fetchMinecraftVersions()
    {
        // URL: https://launchermeta.mojang.com/mc/game/version_manifest.json
        $curl = curl_init("http://launchermeta.mojang.com/mc/game/version_manifest.json");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl); //contains json
        curl_close($curl);

        echo $output; //return to browser
    }

    public function fetchDownloadURLS($input)
    {
        // URI: https://launchermeta.mojang.com/mc/game/%hash%/%version%.json
        $minecraftVersionUrl = $input['url'];
        $curl = curl_init($minecraftVersionUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($curl);
        curl_close($curl);

        return json_decode($output, 1);
    }

    /**
     * Minecraft Server Status Query
     *
     * @link        https://github.com/FunnyItsElmo/PHP-Minecraft-Server-Status-Query/
     * @author      Julian Spravil <julian.spr@t-online.de>
     * @copyright   Copyright (c) 2016 Julian Spravil
     * @license     https://github.com/FunnyItsElmo/PHP-Minecraft-Server-Status-Query/blob/master/LICENSE
     */

    public function getStatus($host, $port = 25565)
    {
        //import
        require_once APP . '/core/status/packet.php';
        require_once APP . '/core/status/handshakePacket.php';
        require_once APP . '/core/status/pingPacket.php';

        $host =  filter_var($host, FILTER_VALIDATE_IP) ? $host: gethostbyname($host);
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (! @socket_connect($socket, $host, $port)) {
            return json_encode((object) array("status"=>"Offline"));
        }

        // create the handshake and ping packet
        $handshakePacket = new HandshakePacket($host, $port, 107, 1);
        $pingPacket = new PingPacket();

        $handshakePacket->send($socket);

        // high five
        $start = microtime(true);
        $pingPacket->send($socket);
        $length = $this->readVarInt($socket);
        $ping = round((microtime(true) - $start) * 1000);

        // read the requested data
        $data = socket_read($socket, $length, PHP_NORMAL_READ);
        $data = strstr($data, '{');
        $data = json_decode($data);

        $descriptionRaw = isset($data->description) ? $data->description : false;
        $description = $descriptionRaw;

        // colorize the description if it is supported
        if (gettype($descriptionRaw) == 'object') {
            $description = '';

            if (isset($descriptionRaw->text)) {
                $color = isset($descriptionRaw->color) ? $descriptionRaw->color : '';
                $description = '<font color="' . $color . '">' . $descriptionRaw->text . '</font>';
            }

            if (isset($descriptionRaw->extra)) {
                foreach ($descriptionRaw->extra as $item) {
                    $description .= isset($item->bold) && $item->bold ? '<b>' : '';
                    $description .= isset($item->color) ? '<font color="' . $item->color . '">' . $item->text . '</font>' : '';
                    $description .= isset($item->bold) && $item->bold ? '</b>' : '';
                }
            }
        }

        return json_encode(array(
                'status' => 'Online',
                'hostname' => $host,
                'port' => $port,
                'ping' => $ping,
                'version' => isset($data->version->name) ? $data->version->name : false,
                'protocol' => isset($data->version->protocol) ? $data->version->protocol : false,
                'players' => isset($data->players->online) ? $data->players->online : false,
                'max_players' => isset($data->players->max) ? $data->players->max : false,
                'description' => $description,
                'description_raw' => $descriptionRaw,
                'favicon' => isset($data->favicon) ? $data->favicon : false,
                'modinfo' => isset($data->modinfo) ? $data->modinfo : false
        ));

    }

    private function readVarInt ($socket) {
       $a = 0;
       $b = 0;
       while (true) {
           $c = socket_read($socket, 1);
           if (! $c) {
               return 0;
           }
           $c = Ord($c);
           $a |= ($c & 0x7F) << $b ++ * 7;
           if ($b > 5) {
               return false;
           }
           if (($c & 0x80) != 128) {
               break;
           }
       }
       return $a;
   }
} //end minecraft class
