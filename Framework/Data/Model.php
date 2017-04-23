<?php
namespace Framework\Data;

use Framework\Data\Builder;
use Framework\Data\Connector;

class Model
{
    // SQL命令构建器, Builder类
    protected $builder;

    // 数据库返回的数据存在这里
    protected $data;

    // 数据库表名, 选填, 默认为类名
    protected $table;

    // 主键, 二选一($unique)
    protected $identity;

    // unique key, 二选一($identity)
    protected $unique;

    /**
     * getTable - 获取数据库表名, 没有设置返回false
     * @return bool
     */
    public function getTable()
    {
        return isset($this->table) ? $this->table : false;
    }

    /**
     * getIdentity - 获取主键名, 没有返回假
     * @return bool
     */
    public function getIdentity()
    {
        return isset($this->identity) ? $this->identity : false;
    }

    /**
     * getUnique - 获取unique key名, 没有返回假
     * @return array|bool
     */
    public function getUnique()
    {
        // 检测是否存在unique key, 不存在返回假, 存在就在检查是否数组, 不是就装入数组再返回
        return isset($this->unique) ? is_array($this->unique) ? $this->unique : [$this->unique] : false;
    }

    /**
     * check - 检查必须预设的实例属性
     */
    public function check()
    {
        // 如果数据库表的名称和Model的子类相同,可以选择不填,默认直接取类的名称
        if (!$this->getTable())
            $this->table = get_class($this);

        // 跳出提醒必须设置$identity或$unique其中一项
        if (!$this->getIdentity() && !$this->getUnique())
            throw new Exception('One of $identity or $unique should be assigned in Model "' . get_called_class() . '"');

    }

    // 设置Builder实例
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
        return $this;
    }

    // 获取Builder实例
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * setData - 设置数据库数据
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function __construct()
    {
        // 检查设定是否正确
        $this->check();
        // 新建一个Builder实例
        $this->setBuilder(new Builder);
        // 设置构建器的主表名称
        $this->getBuilder()->from($this->table);
        // 将Model实例带入Builder
        $this->getBuilder()->setModel($this);
    }

    static public function __callStatic($method, $args = null)
    {
        // 这是一个伪静态, 创建一个实例
        $instance = new static;
        // 在$instance->builder之中, 寻找函数$method, 并附上参数$args
        return call_user_func_array([$instance->builder, $method], $args);
    }

    public function __call($method, $args)
    {
        // 在$this->builder之中, 寻找函数$method, 并附上参数$args
        return call_user_func_array([$this->builder, $method], $args);
    }

    public function __debugInfo()
    {
        // 也不懂算不算bug, 该方法强制要求返回的数据类型必须是array数组
        // 但是就算我强行转换(casting)后返回的数据依然是对象(object)
        return (array)$this->data;
    }

    // 为了避免局外人可以访问Model类的属性
    // 为了避免对象属性和表的字段名字相同
    public function __get($field)
    {
        // 如果调用的属性是Model类内的逻辑
        // 直接返回该属性的值
        if (get_called_class() === "Model")
            return $this->$field;
        // 反之, 则检查$data内是否存在该属性, 没有的话跳出错误
        if (!isset($this->data->$field))
            throw new Exception("column '$field' is not exists in table '$this->table'");
        // 如果存在,由于返回的数据都是存在$data里, 所以要这样调用
        return $this->data->$field;
    }

    /**
     * __set - 当想修改的对象属性时, 强制调用这个魔术方法
     * @param $field
     * @param $value
     * @return mixed
     */
    public function __set($field, $value)
    {
        // 如果调用的属性是Model类内的逻辑
        // 直接赋值该属性
        if (get_called_class() === "Model")
            return $this->$field = $value;
        // 反之, 则检查$data内是否存在该属性, 没有的话跳出错误
        if (!isset($this->data->$field))
            throw new Exception("column '$field' is not exists in table '$this->table'");

        // 如果存在,由于返回的数据都是存在$data里, 所以要这样赋值
        return $this->data->$field = $value;
    }


}