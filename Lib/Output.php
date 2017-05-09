<?php
namespace CompactTp\Lib;

class Output
{
    private static $smarty=null;
    private static $var=array();
    /**
     * 错误输出（1001为正确，1002为发送参数错误，1003为页面错误）
     * @param $msg
     * @param int $ret
     */
    public static function error($msg,$ret=1003)
    {
        $format=&$_GET['format'];
        if($format=='json'){
            echo json_encode(array(
                'ret'=>$ret,
                'msg'=>$msg
            ));
            exit(1);
        }
        self::assign('msg',$msg);
        self::display('public/error.html');
        exit;
    }
    /**
     * 显示
     */
    public static function display($tpl, $var = array()) {
        header('Content-Type:text/html; charset=utf-8');

        $format = &$_GET['format'];

        if ($format == "json") {
            echo json_encode(array(
                'ret' => 11,
                'data' => self::$var,
            ));
            return;
        }

        $smarty = self::getSmarty();
        self::$var = array_merge(self::$var, $var);
        if (self::$var)
            $smarty->assign(self::$var);

        $smarty->display($tpl);
    }
    public static function assign($key, $val = NULL) {
        if (is_array($key)) {
            self::$var = self::$var ? array_merge(self::$var, $key) : $key;
        } else {
            self::$var[$key] = $val;
        }
    }
    private static function getSmarty() {
        if (!self::$smarty) {
            // 初始化smarty
            require_once dirname(__DIR__) . '/Lib/smarty/Smarty.class.php';
            $smarty = new \Smarty();
            $smarty->left_delimiter = '{{';
            $smarty->right_delimiter = '}}';
            $smarty->caching = false;
            $smarty->debugging = false;
            $smarty->compile_check = true;
            $smarty->cache_dir = dirname(__DIR__) . '/Data/cache';
            $smarty->compile_dir = dirname(__DIR__) . '/Data/templates_c';
            $smarty->template_dir = dirname(__DIR__) . '/View/';

            self::$smarty = $smarty;
        }
        return self::$smarty;
    }
}

