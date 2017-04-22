<?php
require(__DIR__ . '/vendor/autoload.php');

require(__DIR__.'/Framework/Core/App.php');

define('BASE_PATH', __DIR__);

$config = require BASE_PATH.'/config/config.php';
date_default_timezone_set($config['time_zone']);

// Log
if (!is_dir(BASE_PATH.'/logs/')) {
    mkdir(BASE_PATH.'/logs/', 0700);
}
$monolog = new \Monolog\Logger('system');
$monolog->pushHandler(new \Monolog\Handler\StreamHandler(BASE_PATH.'/logs/app.log', \Monolog\Logger::DEBUG));
//$monolog->addDebug('test');





