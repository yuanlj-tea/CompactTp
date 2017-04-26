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
                return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]]:$default;
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

