<?php
$dev=[
    //默认控制器和方法
    'router'   => array(
        'default' => array('Index', 'index'),
    ),
    //数据库连接相关配置
    'database' => array(
        'driver'=>'mysql',
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'test',
        'username' => 'root',
        'password' => 'root',
        'charset'   =>'utf8',
    ),

    'memcache' => array(
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'predix' => 'office_gold_' //cache
    )

];

return $dev;