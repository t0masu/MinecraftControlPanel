<?php
class minecraft
{
    public function __construct(){}

    public function fetchMinecraftVersions()
    {
        // URL: https://launchermeta.mojang.com/mc/game/version_manifest.json
        $curl = curl_init("https://launchermeta.mojang.com/mc/game/version_manifest.json");
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

        $output = curl_exec($curl);
        curl_close($curl);

        return json_decode($output, 1);
    }
} //end minecraft class
