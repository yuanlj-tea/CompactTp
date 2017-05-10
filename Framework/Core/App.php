<?php

namespace Framework\Core;

use Framework\Core\Router;

class App
{
    public static $tr_pairs = array()
    , $is_cli = false
    , $options = array();

    public static function run($app_dir)
    {
        self::initConst($app_dir);
        self::initConfig();
        self::initError();
        self::setOption($GLOBALS['config']);
        self::initAutoLoadRegister();
        self::dispatch(self::getPathInfo());

    }

    /**
     * cli.php uid=1&action=abc method=send
     * php cli.php crontab/index123?a=c  name=123123
     * @param $app_dir
     * @throws Exception
     */
    public static function cli($app_dir)
    {
        $argv = $_SERVER['argv'];

        if (!isset($argv[1]))
            throw new \Exception('Please input Controller/Action');

        // treated as $_POST
        if (isset($argv[2]))
            \parse_str($argv[2], $_POST);

        $argv = \explode('?', $argv[1]);

        // treated as $_GET
        if (isset($argv[1]))
            \parse_str($argv[1], $_GET);

        self::$is_cli = true;

        self::initConst($app_dir);
        self::initConfig();
        self::initError();
        self::setOption($GLOBALS['config']);
        self::initAutoLoadRegister();

        self::dispatch($argv[0]);
    }

    public static function setOption($key, $val = null)
    {
        if (\is_array($key)) {
            self::$options = $key + self::$options;
        } elseif ($key) {
            self::$options[$key] = $val;
        }

        return self::$options;
    }

    public static function getOption($key, array $options = array())
    {
        if (isset(self::$options[$key]))
            return $options + self::$options[$key];

        return $options;
    }

    public static function getPathInfo()
    {
        if (isset($_GET['r'])) {
            return str_replace('www', '', $_GET['r']);
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function dispatch($url)
    {
        $query = Router::parse($url);

        define('CONTROLLER', $query[0]);
        define('ACTION', $query[1]);

        $class = APP_NS . 'Controller\\' . CONTROLLER;

        //p($class, 1);
        if (!method_exists($class, ACTION)) {
            header('location:/404.html');
            exit;
        }

        return self::getController(CONTROLLER)->{ACTION}();
    }

    public static function getController($name)
    {
        $class = APP_NS . 'Controller\\' . $name;
        //p($class,1);
        if (\class_exists($class)) {
            return new $class;
        }

        header('location:/404.html');
//        throw new \Parith\Exception('Controller "' . $name . '" not found', 404);
        return false;
    }

    /**
     * 定义路径常量
     */
    public static function initConst($app_dir)
    {
        define('DS', DIRECTORY_SEPARATOR);    //分隔符
        define('PUBLIC_PATH', getcwd() . DS);   //Public目录,index.php所在目录
        defined('ROOT_PATH') or define('ROOT_PATH', BASE_PATH . DS);   //项目所在目录
        define('APP_PATH', ROOT_PATH . 'App' . DS);   //App目录
        define('FRAME_PATH', ROOT_PATH . 'Framework' . DS);   //Framework目录
        define('CONFIG_PATH', ROOT_PATH . 'Config' . DS);      //Config目录
        define('CONTROLLER_PATH', ROOT_PATH . 'Controller' . DS);  //Controller目录
        define('MODEL_PATH', ROOT_PATH . 'Model' . DS);    //Model目录
        define('VIEW_PATH', APP_PATH . 'View' . DS);  //View目录
        define('CORE_PATH', FRAME_PATH . 'Core' . DS);    //Core目录
        define('LIB_PATH', FRAME_PATH . 'Lib' . DS);  //Lib目录
        define('HELP_PATH', FRAME_PATH . 'Helpers' . DS);     //帮助函数目录
        define('UPLOADS_PATH', PUBLIC_PATH . 'Uploads' . DS);     //上传图片和生成的缩略图保存的目录
        define('LOG_PATH', APP_PATH . 'Log' . DS);    //错误日志保存的文件夹目录

        //\define('APP_DIR', $app_dir . DIRECTORY_SEPARATOR);
        \define('APP_NS', basename(ROOT_PATH) . '\\');

        // now time
        define('APP_TS', \time());

        self::$tr_pairs = array(APP_NS => ROOT_PATH, 'Framework\\' => FRAME_PATH, '\\' => DIRECTORY_SEPARATOR);


        // Parith Exception handler
        //\set_error_handler('\Parith\Exception::error');
        //\set_exception_handler('\Parith\Exception::handler');
    }

    /**
     * 根据开发模式加载对应配置文件
     */
    public static function initConfig()
    {
        $GLOBALS['conf'] = require(CONFIG_PATH . 'config.php');
        if ($GLOBALS['conf']['debug']) {
            $GLOBALS['config'] = require(CONFIG_PATH . 'dev.php');
            C(load_config(CONFIG_PATH . 'dev.php'));
        } else {
            $GLOBALS['config'] = require(CONFIG_PATH . 'online.php');
            C(load_config(CONFIG_PATH . 'online.php'));
        }
    }

    /**
     * 定义错误显示方式
     */
    public static function initError()
    {
        ini_set('error_reporting', E_ALL | E_STRICT);    //显示所有错误
        if ($GLOBALS['conf']['debug']) {     //开发模式
            ini_set('display_errors', 'on');             //在浏览器上显示错误
            ini_set('log_errors', 'off');                //不记录错误日志

            // whoops: php errors for cool kids
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();

        } else {      //运行模式
            ini_set('display_errors', 'off');        //在浏览器上不显示错误
            ini_set('log_errors', 'on');             //开启记录错误日志
            $log_name = 'php_error_' . date('Y-m-d');                //错误日志名
            $log_path = LOG_PATH . $log_name . '.log';    //错误日志保存路径
            ini_set('error_log', $log_path);         //保存错误日志
        }
    }


    public static function initAutoLoadRegister()
    {
        \spl_autoload_register('self::autoload');
    }


    public static function autoload($class_name)
    {
        //echo $class_name.'<br>';
        /*$className = substr($class_name, strrpos($class_name, DS) + 1);
        $class_map = array(
            'Router' => CORE_PATH . 'Router.php',

        );
        if (isset($class_map[$className])) {
            require $class_map[$className];
        }*/
        return self::import($class_name . '.php', false);
    }

    /**
     * @static
     * @param $name
     * @param bool $throw
     * @return bool|mixed
     * @throws Exception
     */
    public static function import($name, $throw = true)
    {
        $name = self::parseName($name);

        if (\is_file($name)) {
            return include $name;
        }

        if ($throw) {
            throw new \Parith\Exception('File "' . $name . '" is not exists');
        }

        return false;
    }

    /**
     * @static
     * @param $name
     * @return string
     */
    public static function parseName($name)
    {
        return \strtr($name, self::$tr_pairs);
    }
}