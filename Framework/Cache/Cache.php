<?php
namespace Framework\Cache;

use Framework\Core\App;
use Framework\Core\Arr;

class Cache
{
    public $options = array();

    private $_cache = array();

    public function __construct(array $options = array())
    {
        $this->options = App::getOption('cache', $options) + $this->options;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return Arr::get($this->_cache, $key, null);
    }

    /**
     * @param $key
     * @param $val
     * @return bool
     */
    public function set($key, $val)
    {
        $this->_cache[$key] = $val;
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        unset($this->_cache[$key]);
        return true;
    }

    /**
     * return bool
     */
    public function flush()
    {
        $this->_cache = array();
        return true;
    }
}