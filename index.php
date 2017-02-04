<?php
header("Access-Control-Allow-Origin: *");
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);
// load application config (error reporting etc.)
require APP . 'core/config.php';
require APP . 'core/app.php';
require APP . 'core/controller.php';
require APP . 'models/jwt.php';

$app = new Application();
