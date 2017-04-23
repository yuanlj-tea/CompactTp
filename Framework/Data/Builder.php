<?php
namespace Framework\Data;

use Framework\Data\Connector;
use Framework\Data\Grammar;

class Builder
{
    // 连接数据库, Connector类
    protected $connector;

    // 生成SQL语法，Grammar类
    protected $grammar;

    // 连接的Model, Model类
    protected $model;

    // SQL查询语句中条件的值
    // 虽然列出了全部, 但本教程只实现了where,其余的因为懒(理直气壮),请自行实现, 逻辑和where函数大致
    protected $bindings = [
        'select' => [],
        'join' => [],
        'where' => [],
        'having' => [],
        'order' => [],
        'union' => [],
    ];

    // select 语法想要查看的字段
    public $columns;

    // 过滤重复值
    public $distinct = false;

    // 需要查询的表
    public $from;

    // 所有join 语法
    public $joins;

    // 所有where 语法
    public $wheres;

    // group 语法
    public $groups;

    // having 语法
    public $havings;

    // order by 语法
    public $orders;

    // 限制数据库返回的数据量, limit语法
    public $limit;

    // 需要略过的数据量, offset语法
    public $offset;

    // 数据写保护， 开启后该条数据无法删除或改写
    public $writeLock = false;

    public function __construct()
    {
        // 新建两个实例
        // 如果已经理解Connector的原理后自然明白这个Connector实例已经联通了数据库
        $this->connector = new Connector($GLOBALS['config']['database']);
        $this->grammar = new Grammar;
    }

    public function select($columns = ['*'])
    {
        // $columns只能存入数组, 所以需要判定, 如果不是, 将所有参数合成一个数组再存入
        // 这是一个更人性化的设定, 用户可以选择以下两种调用方式
        // select(['first_name', 'last_name']), 以数组的方式
        // select('first_name', 'last_name'), 以参数的方式
        // 最后一点, 你可以发现所有函数最后都会存入对应的Builder属性中
        // 这和你在做饭前先处理材料是同一个道理, 也就是预处理
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function distinct()
    {
        // 开启过滤
        $this->distinct = true;
        return $this;
    }

    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * @param  string $table 需要连接的副表名
     * 为什么主键和外键可以单个或数组呢
     * 原因是join语法可以on多个键
     * @param  string /array $foregin 外键
     * @param  string /array $primary 主键
     * @param  string $type 连接方式, 默认inner
     * @return Builder          返回Builder实例
     */
    public function join($table, $foregin, $primary, $type = 'inner')
    {
        // 判定外键变量的数据类型
        if (is_array($foregin)) {
            // 如果是数组, 循环加上副表名在前头
            foreach ($foregin as &$f)
                $f = $table . "." . $f;
        } else {
            // 反之, 不需循环直接加
            $foregin = $table . "." . $foregin;
        }

        // 与$foreign的逻辑同理
        if (is_array($primary)) {
            foreach ($primary as &$p)
                $p = $this->from . "." . $p;
        } else {
            $primary = $this->from . "." . $primary;
        }

        // 将所有经过处理的参数收入$joins待用
        $this->joins[] = (object)[
            'from' => $this->from,
            'table' => $table,
            'foregin' => $foregin,
            'primary' => $primary,
            'type' => $type
        ];

        // 返回Builder实例
        return $this;
    }

    // 所有逻辑同join(), 不过这是left join
    public function leftJoin($table, $foregin, $primary)
    {
        return $this->join($table, $foregin, $primary, 'left');
    }

    // 所有逻辑同join(), 不过这是right join
    public function rightJoin($table, $foregin, $primary)
    {
        return $this->join($table, $foregin, $primary, 'right');
    }

    /**
     * @param string /array $value 字段匹配的值
     * @param string $type 条件类型, 默认为where, 具体查看$bindings
     */
    public function addBinding($value, $type = 'where')
    {
        // 如果$type并不是$bindings的键, 跳出错误
        if (!array_key_exists($type, $this->bindings))
            throw new InvalidArgumentException("Invalid binding type: {$type}.");

        // 如果$value是数组,将其与之前存入的值整合为一维数组
        if (is_array($value))
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        // 反之, 直接存入最后一位即可
        else
            $this->bindings[$type][] = $value;

        // 返回Builder实例
        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // 判定$column是否为数组
        if (is_array($column)) {
            // 如果是数组, 循环再调用本函数,where()
            foreach ($column as $key => $value)
                $this->where($key, "=", $value, $boolean);
        } else {
            // 反之, 判定参数数量和$value是否为空, 如果为真,这意味着用户省略了'=',自动添加
            if (func_num_args() == 2 || is_null($value)) list($operator, $value) = ['=', $operator];

            // 最简单原始的条件查询, 所以$type值为Basic
            $type = "Basic";
            // 将处理过的条件存入$wheres
            $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');
            // 将字段需要匹配的值存入$bindings中的where
            $this->addBinding($value, 'where');
        }

        // 返回Builder实例
        return $this;
    }

    // 所有逻辑同where(), 不过这是or where
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * where in 语法, 字段匹配多个值
     * @param  string $column 字段
     * @param  array $values 一组字段需匹配的值
     * @param  string $boolean 默认为and
     * @param  boolean $not 默认为假, 真为排除所有$value里的数据
     * @return Builder           返回Builder实例
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        // 判定条件查询的类型, false = where in ($value),true = where not in ($value)
        $type = $not ? 'NotIn' : 'In';
        // 将条件存入$wheres
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        // 循环将字段需要匹配的值存入$bindings中的where
        foreach ($values as $value)
            $this->addBinding($value, 'where');

        // 返回Builder实例
        return $this;
    }

    // 所有逻辑同whereIn(), 不过这是or where in
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    // 所有逻辑同whereIn(), 不过这是and where not in
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    // 所有逻辑同whereNotIn(), 不过这是or where not in
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * where $coulmn is null 语法, 搜索字段为空的数据
     * @param  string $column 字段
     * @param  string $boolean 默认为and
     * @param  boolean $not 默认为假, 真为排除所有字段为空的数据
     * @return Builder          返回Builder实例
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        // 判定条件查询的类型, false = where $column is null,true = where $column is not null
        $type = $not ? 'NotNull' : 'Null';
        // 将条件存入$wheres
        $this->wheres[] = compact('type', 'column', 'boolean');
        // 返回Builder实例
        return $this;
    }

    // 所有逻辑同whereNull(), 不过这是or where $column is null
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    // 所有逻辑同whereNull(), 不过这是and where $column is not null
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    // 所有逻辑同whereNotNull(), 不过这是or where $column is not null
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * @param  string /array $column    字段
     * @param  string $direction 排序,默认为asc, 顺序
     * @return Builder            返回Builder实例
     */
    public function orderBy($column, $direction = 'asc')
    {
        // 局限性在于必须声明顺序或逆序
        if (is_array($column)) {
            foreach ($column as $key => $value)
                $this->orderBy($key, $value);
        } else {
            // 简单判定后直接存入$orders, $direction输入错误不跳错误直接选择顺序
            $this->orders[] = [
                'column' => $column,
                'direction' => strtolower($direction) == 'desc' ? 'desc' : 'asc',
            ];
        }

        // 返回Builder实例
        return $this;
    }

    /**
     * @param  string /array $groups 字段
     * @return Builder        返回Builder实例
     */
    public function groupBy($groups)
    {
        if (empty($this->groups)) $this->groups = [];
        $this->groups = array_merge($this->groups, array_flatten($groups));
        // 返回Builder实例
        return $this;
    }

    public function limit($value)
    {
        // 如果$value大于零这条函数才生效
        if ($value >= 0) $this->limit = $value;
        return $this;
    }

    // limit函数的别名, 增加函数链的可读性
    public function take($value)
    {
        return $this->limit($value);
    }

    public function offset($value)
    {
        // 如果$value大于零这条函数才生效
        if ($value >= 0) $this->offset = $value;
        return $this;
    }

    // offset函数的别名, 增加函数链的可读性
    public function skip($value)
    {
        return $this->offset($value);
    }

    // 返回一组数据库数据, 可以在这里设定想返回的字段, 但是select()的优先度最高
    public function get($columns = ['*'])
    {
        // 如果Builder的$columns依然为空, 那么就用该函数的$columns, 反之则使用select()所声明的字段
        if (is_null($this->columns)) $this->columns = $columns;
        // 如果Builder的$orders依然为空, 那么就默认第一个字段顺序
        // 发现一个莫名的bug, 可能是我理解错了, 不加 order by 1数据返回竟然不是按照主键(第一个字段)排序
        // 所以以防万一加一个默认
        if (is_null($this->orders)) $this->orderBy(1);
        // 将Grammar类生成的语句,和处理过的字段所对应的值,都交给Connector类, 让它与数据库进行通信,返回数据
        // 注意这里的三个函数
        // read() 不用说Connector篇介绍过了
        // compileSelect()是用来编译生成查询语句
        // getBindings()用来获取收在$bindings中条件的值, 下方会有说明
        $results = $this->connector->read($this->grammar->compileSelect($this), $this->getBindings());
        // 返回一组数据库数据,如果查询为空,返回空数组
        // cast()下方会有说明
        return $this->cast($results);
    }

    // get函数的别名, 增加函数链的可读性
    public function all($columns = ['*'])
    {
        return $this->get($columns);
    }

    public function getBindings()
    {
        // 抚平多维数组成一维数组后再返回
        return array_flatten($this->bindings);
    }

    public function cast($results)
    {
        // 获取Model子类的名称
        $class = get_class($this->model);
        // 新建一个Model子类
        $model = new $class();
        // 如果获得的数据库数据是数组
        if (gettype($results) == "array") {
            $arr = [];
            // 循环数据
            foreach ($results as $result)
                // 再调用本函数
                $arr[] = $this->cast($result);
            // 返回经过转化的数据数组
            return $arr;
            // 如果获得的数据库数据是对象
        } elseif (gettype($results) == "object") {
            // 存入数据对象
            $model->setData($results);
            // 加入主键或unique key以实现数据的可操作性
            // 如果表存在主键和返回的数据中有主键的字段
            if ($model->getIdentity() && isset($results->{$model->getIdentity()})) {
                $model->where($model->getIdentity(), $results->{$model->getIdentity()});
                // 如果表存在unique key和返回的数据中有unique key的字段
            } elseif ($model->getUnique() && array_check($model->getUnique(), $results)) {
                foreach ($model->getUnique() as $key)
                    $model->where($key, $results->$key);
                // 改写和删除操作仅仅在符合以上两种条件其中之一的时候
                // 反之, 开启写保护不允许改写
            } else {
                // 其实还可以考虑直接复制query
                // 但变数太多干脆直接一棍子打死
                $model->getBuilder()->writeLock = true;
            }
            // 返回该实例
            return $model;
        }
        // 如果转化失败返回false
        return false;
    }

    /**
     * @param  array $columns 如果Builder的$columns依然为空, 那么就用该函数的$columns, 反之则使用select()所声明的字段
     * @return boolean/Model          查询为空返回false, 反之则返回附带数据的表类
     */
    public function first($columns = ['*'])
    {
        $results = $this->take(1)->get($columns);
        return empty($results) ? false : $results[0];
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    // 改写数据库数据
    public function update(array $values)
    {
        // 如果写保护已经开启，跳出错误
        if ($this->writeLock) throw new Exception("data is not allow to update");

        // 编译update语句
        $sql = $this->grammar->compileUpdate($this, $values);

        // 将所有变量的值合成一个数组， 其中包括条件语句部分
        $bindings = array_values(array_merge($values, $this->getBindings()));

        // 返回改写结果，成功true失败false
        return $this->connector->update($sql, $bindings);
    }
}