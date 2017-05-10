<?php
require(__DIR__.'/../bootstrap.php');
$dir = dirname(__DIR__);
use Framework\Core\App;

App::cli($dir);