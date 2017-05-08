<?php

/* 日志类
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CompactTp\Lib;

class Log
{

    const LOG_PATH = '/Data/logs/'; //设置日志路径
    /**
     * 记录基本信息
     * @param array $params 要记录的内容
     */

    public static function addLog($tip, $dir = 'main', $params = null)
    {
        $currtime = time();
        if (!@file_exists(self::LOG_PATH . $dir)) {
            @mkdir(self::LOG_PATH . $dir, 0777);
        }
        $fname = dirname(dirname(__FILE__)) . self::LOG_PATH . $dir . date("Ymd", $currtime) . '.log';
        $mesg = date("Y-m-d H:i:s") . "\tl=info,t=" . $tip . "\t";
        foreach ($params as $k => $v) {
            $mesg .= $k . '=' . $v . ',';
        }
        if (!@file_exists($fname)) {
            @touch($fname);
            @chmod($fname, 0777);
        }
        error_log($mesg . "\n", 3, $fname);
    }

    public static function addSqlLog($sql)
    {
        $query = trim($_SERVER['REQUEST_URI'],'/');
        if (strpos($query, '&') > 0) {
            $query = substr($query, 0, strpos($query, '&'));
        }
        $action = str_replace('/', '_', $query);
        $action = empty($action) ? 'index' : $action;
        $dir = dirname(dirname(__FILE__)) . '/Data/logs/' ;
        if (!@file_exists($dir)) {
            @mkdir($dir, 0777,true);
        }
        $fname = $dir . $action .'.log';
        $mesg = "[".date("Y-m-d H:i:s")."]----" . $sql."\n";
        if (!@file_exists($fname)) {
            @touch($fname);
            @chmod($fname, 0777);
        }
        //error_log($mesg, 3, $fname);
    }

}

?>
