<?php
if (!function_exists('p')) {
    function p($data, $flag = 0)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($flag) {
            die;
        }
    }
}
if (!function_exists('array_flatten')) {
    function array_flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }
}
if(!function_exists('C')){
    function C($name=null,$value=null,$default=null){
        static $_config=array();
        //无参数时获取所有
        if(empty($name)){
            return $_config;
        }
        //优先执行设置获取或赋值
        if(is_string($name)){
            if(!strpos($name,'.')){
                $name=strtoupper($name);
                if(is_null($value)){
                    return isset($_config[$name])? $_config[$name]: $default;
                }
                $_config[$name]=$value;
                return null;
            }
            //二维数组设置和获取支持
            $name=explode('.',$name);
            $name[0]=strtoupper($name[0]);
            if(is_null($value)){
                return isset($_config[$name[0]][$name[1]]) ? $config[$name[0]][$name[1]]:$default;
            }
            $_config[$name[0]][$name[1]]=$value;
            return null;
        }
        //批量设置
        if(is_array($name)){
            $_config=array_merge($_config,array_change_key_case($name,CASE_UPPER));
            return null;
        }
        return null;    //避免非法参数
    }
}

/**
 * 实例化模型类 格式 [资源://][模块/]模型
 * @param string $name 资源地址
 * @param string $layer 模型层名称
 * @return Think\Model
 */
function D($name='',$layer='') {
    if(empty($name)) return new Think\Model;
    static $_model  =   array();
    $layer          =   $layer? : C('DEFAULT_M_LAYER');
    if(isset($_model[$name.$layer]))
        return $_model[$name.$layer];
    $class          =   parse_res_name($name,$layer);
    if(class_exists($class)) {
        $model      =   new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        // 自动加载公共模块下面的模型
        if(!C('APP_USE_NAMESPACE')){
            import('Common/'.$layer.'/'.$class);
        }else{
            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
        }
        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
    }else {
        Think\Log::record('D方法实例化没找到模型类'.$class,Think\Log::NOTICE);
        $model      =   new Think\Model(basename($name));
    }
    $_model[$name.$layer]  =  $model;
    return $model;
}
