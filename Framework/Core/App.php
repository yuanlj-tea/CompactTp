<?php
namespace Framework\Core;

class App{
    public static function run($dir)
    {
        self::initConst();
        self::initConfig();
        self::initError();
        self::initAutoLoadRegister();

        echo CONTROLLER_PATH;die;
    }

    /**
     * 定义路径常量
     */
    public static function initConst()
    {
        define('DS',DIRECTORY_SEPARATOR);    //分隔符
        define('PUBLIC_PATH',getcwd().DS);   //Public目录,index.php所在目录
        defined('ROOT_PATH') or define('ROOT_PATH',BASE_PATH.DS);   //项目所在目录
        define('APP_PATH',ROOT_PATH.'App'.DS);   //App目录
        define('FRAME_PATH',ROOT_PATH.'Framework'.DS);   //Framework目录
        define('CONFIG_PATH',ROOT_PATH.'Config'.DS);      //Config目录
        define('CONTROLLER_PATH',APP_PATH.'Controller'.DS);  //Controller目录
        define('MODEL_PATH',APP_PATH.'Model'.DS);    //Model目录
        define('VIEW_PATH',APP_PATH.'View'.DS);  //View目录
        define('CORE_PATH',FRAME_PATH.'Core'.DS);    //Core目录
        define('LIB_PATH',FRAME_PATH.'Lib'.DS);  //Lib目录
        define('HELP_PATH',FRAME_PATH.'Helpers'.DS);     //帮助函数目录
        define('UPLOADS_PATH',PUBLIC_PATH.'Uploads'.DS);     //上传图片和生成的缩略图保存的目录
        define('LOG_PATH',APP_PATH.'Log'.DS);    //错误日志保存的文件夹目录
    }

    /**
     * 根据开发模式加载对应配置文件
     */
    public static function initConfig()
    {
        $GLOBALS['conf']=require(CONFIG_PATH.'config.php');
        if($GLOBALS['conf']['debug']){
            $GLOBALS['config']=require (CONFIG_PATH.'dev.php');
        }else{
            $GLOBALS['config']=require (CONFIG_PATH.'online.php');
        }
    }

    public static function initError()
    {
        ini_set('error_reporting',E_ALL | E_STRICT);    //显示所有错误
        if($GLOBALS['conf']['debug']){     //开发模式
            ini_set('display_errors','on');             //在浏览器上显示错误
            ini_set('log_errors','off');                //不记录错误日志

            // whoops: php errors for cool kids
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();

        }else{      //运行模式
            ini_set('display_errors','off');        //在浏览器上不显示错误
            ini_set('log_errors','on');             //开启记录错误日志
            $log_name='PHP_ERROR-'.date('Y-m-d');                //错误日志名
            $log_path=LOG_PATH.$log_name.'.log';    //错误日志保存路径
            ini_set('error_log',$log_path);         //保存错误日志
        }
    }

    public static function initAutoLoadRegister()
    {
        \spl_autoload_register('self::autoload');
    }

    public static function autoload()
    {

    }
}