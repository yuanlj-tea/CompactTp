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
