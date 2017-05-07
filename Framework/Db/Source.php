<?php
namespace Framework\Db;

abstract class Source
{
    public static $options=array(
        'host'=>'127.0.0.1',
        'port'=>0,
    );

    public $configs=array();

    protected $link,$connected=false;

    public function __construct(array $options=array())
    {
        if($options){
            $this->connect($options);
        }
    }
    /**
     * disconnect from server
     * @abstract
     */
    abstract public function close();
    /**
     * @abstract
     * @param array $options
     * @return mixed
     */
    abstract public function connect(array $options);

    /**
     * @param $options
     */
    public static function connection($options)
    {
        return \Framework\Core\Object::getInstance(\get_called_class(),\func_get_args(),static::instanceKey($options));
    }

    /**
     * @param $options
     * @return string
     */
    public static function instanceKey($options)
    {
        if(is_array($options)){
            return $options['host'].':'.$options['port'];
        }
        return $options;
    }
    /**
     * an Overwrite example:
     *
     * public static function option($cfg_id)
     * {
     *      $servers = array (
     *          1 => array('host' => '127.0.0.1', 'port' => 11211),
     *          2 => array('host' => '127.0.0.1', 'port' => 11212),
     *      );
     *
     *      return parent::option($servers[$cfg_id]);
     * }
     *
     * @static
     * @param array $options
     * @return array|bool
     * @throws \Parith\Exception
     */
    public static function option($options)
    {
        if (is_array($options))
            return $options + static::$options;

        throw new \Framework\Core\Exception('options must be an Array');

        return false;
    }
}