<?php
require(__DIR__ . '/vendor/autoload.php');
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

define("MOMOMA_PATH", dirname(__FILE__)); //项目根目录

// Config
$conf = new \Noodlehaus\Config(MOMOMA_PATH. '/Config/conf.php');
echo $conf->get('debug');
//echo $conf['debug'];

