<?php
require(__DIR__ . '/vendor/autoload.php');
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler(__DIR__.'/test.log', Logger::DEBUG ));

// add records to the log
$log->debug('Foo');
$log->error('Bar');
$log->addInfo('My logger is now ready');

define("MOMOMA_PATH", dirname(__FILE__)); //项目根目录
echo $a;
// Config
$conf = new \Noodlehaus\Config(MOMOMA_PATH. '/Config/conf.php');
echo $conf->get('debug');
//echo $conf['debug'];

