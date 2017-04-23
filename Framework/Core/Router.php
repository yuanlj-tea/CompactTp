<?php
namespace Framework\Core;

use Framework\Core\App;

class Router
{
    public static $options = array(
        'delimiter' => '/',
        'rules' => array(),
        'accept' => array('c', 'a'),
        'default' => array('Index', 'index'),
    );

    public static function parse($url, array $options = array())
    {
        $options = App::getOption('router', $options) + self::$options;

        if ($url) {
            //$url = explode('?', $url, 2);
            $arr = self::parseURL(trim($url, '/'), $options) + $options['default'];

            $arr[0] = \ucfirst($arr[0]);

            return $arr;
        }

        $arr = $_GET;
        $c = &$arr[$options['accept'][0]] or $c = $options['default'][0];
        $a = &$arr[$options['accept'][1]] or $a = $options['default'][1];

        return array(\ucfirst($c), $a);
    }

    /**
     * @param $url
     * @param $options
     * @return array
     */
    public static function parseURL($url, $options)
    {
        foreach ($options['rules'] as $key => $val) {
            $r = \preg_replace('/^' . $key . '$/i', $val, $url, -1, $n);
            if ($n) {
                $url = $r;
                break;
            }
        }

        return \explode($options['delimiter'], $url);
    }
}