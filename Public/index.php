<?php
header("Content-Type:text/html;charset=utf-8");
require(__DIR__.'/../bootstrap.php');
$dir = dirname(__DIR__);
use Framework\Core\App;

App::run($dir);

//require '../config/routes.php';








/*define("MOMOMA_PATH", dirname(__FILE__)); //项目根目录

// Config
$conf = new \Noodlehaus\Config(MOMOMA_PATH. '/../Config/conf.php');
echo $conf->get('debug');
//echo $conf['debug'];*/



/*
 * 用来封装手动写入日志类
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler(__DIR__.'/test.log', Logger::DEBUG ));
// add records to the log
$log->debug('Foo');
$log->error('Bar');
$log->addInfo('My logger is now ready');*/