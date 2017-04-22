<?php
$online=[
    'router'   => array(
        'default' => array('Index', 'index'),
    ),
    'database' => array(
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'test',
        'username' => 'root',
        'password' => 'root',
    ),

    'memcache' => array(
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'predix' => 'office_gold_' //cache
    )

];

return $online;