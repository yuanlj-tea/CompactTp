<?php
namespace Framework\Core;

class Monitor
{
    public static $log=array();
    private static $_status=array();

    /**
     * @param $name
     * @return array
     */
    public static function mark($name)
    {
        return self::$_status[$name]=array('time'=>\microtime(true),'mem' => \memory_get_usage(), 'peak' => \memory_get_peak_usage());
    }

    /**
     * @param null $start
     * @param null $end
     * @param string $type
     * @param int $decimal
     * @return bool|float
     */
    public static function status($start=null,$end=null,$type='time',$decimal=5)
    {
        if(\count(self::$_status)<2){
            return false;
        }
        $start=isset(self::$_status[$start]) ? self::$_status[$start] : \reset(self::$_status);
        $end = isset(self::$_status[$end]) ? self::$_status[$end] : \end(self::$_status);
        return \round($end[$type]-$start[$type],$decimal);
    }

    /**
     * @param $message
     * @param int $type
     * @return string
     */
    public static function addLog($message,$type=1024)
    {
        return self::$log[]=\date(\DATE_RFC2822,APP_TS). ' ' . $message;
    }

    /**
     * @return array
     */
    public static function getLog(){
        return self::$log;
    }

    public static function writeLog($file=null)
    {
        if(self::$log===array()){
            return false;
        }
        $file or $file=LOG_PATH.\date('Y-m-d',APP_TS).'.log';
        $ret=\error_log(\implode(PHP_EOL,self::$log,3,$file));
        self::$log=array();
        if($ret){
            return $file;
        }
        return false;
    }
}

