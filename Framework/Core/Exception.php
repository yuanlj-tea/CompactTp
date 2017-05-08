<?php
namespace Framework\Core;

class Exception extends \Exception
{
    public static $php_errors = array(
        \E_ERROR => 'Error',
        \E_WARNING => 'Warning',
        \E_PARSE => 'Parse Error',
        \E_NOTICE => 'Notice',
        \E_CORE_ERROR => 'Core Error', // since PHP 4
        \E_CORE_WARNING => 'Core Warning', // since PHP 4
        \E_COMPILE_ERROR => 'Compile Error', // since PHP 4
        \E_COMPILE_WARNING => 'Compile Warning', // since PHP 4
        \E_USER_ERROR => 'User Error', // since PHP 4
        \E_USER_WARNING => 'User Warning', // since PHP 4Parse Error
        \E_USER_NOTICE => 'User Notice', // since PHP 4
        \E_STRICT => 'Strict Notice', // since PHP 5
        \E_RECOVERABLE_ERROR => 'Recoverable Error', // since PHP 5.2.0
        \E_DEPRECATED => 'Deprecated', // Since PHP 5.3.0
        \E_USER_DEPRECATED => 'User Deprecated', // Since PHP 5.3.0
    );

    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message,$code=0)
    {
        $this->message=$message;
        $this->code=$code ? $code : \E_ERROR;
    }

    /**
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws \ErrorException
     */
    public static function error($code,$message,$file,$line)
    {
        $e=new \ErrorException($message,$code,0,$file,$line);
        if(\error_reporting()){
            throw $e;
        }else{
            self::log($e);
        }
    }

    /**
     * @param \Exception $e
     */
    public static function handle(\Exception $e)
    {
        try{
            self::log($e);
            $class = APP_NS.'Controller\Error';

            if(\class_exists($class)){
                $handler=new $class($e);
            }else{
                $handler=new \Parith\Controller\Error($e);
            }
            $handler->index();
        }catch (\Exception $e){
            //self::log($e);
            //\print_r(\Parith\Monitor::getLog());
            //print_r($e->getTrace());
            exit(1);
        }
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function getCodeValue($code)
    {
        if(isset(self::$php_errors[$code])){
            return self::$php_errors[$code];
        }
        return $code;
    }

    /**
     * @param $e
     * @return mixed
     */
    public static function log($e)
    {
        $message=self::text($e);
        Monitor::addLog($message);
        return $message;
    }

    /**
     * @param \Exception $e
     * @param string $format
     * @return string
     */
    public static function text(\Exception $e,$format='[%s] [%s] %s: %d')
    {
        return \sprintf($format,self::getCodeValue($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine()));
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return self::text($this);
    }
}