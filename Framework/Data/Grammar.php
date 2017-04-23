<?php
namespace Framework\Data;

class Grammar
{
    // 构建查询语句所可能出现的各种SQL语法
    // 注意, 它们的顺序是对应着各自在SQL语句中合法的位置
    // sqlsrv略微不同
    protected $selectComponents = [
        'distinct',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'orders',
        'limit',
        'offset',
    ];

    protected function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function ($value) {
            return (string)$value !== '';
        }));
    }

    // 还记得Builder->get()中的compileSelect()吗?
    public function compileSelect(Builder $query)
    {
        // concatenate()排除编译后可能存在空的值,然后连接整句SQL语句
        // 去掉可能存在的前后端空格再返回
        return trim($this->concatenate($this->compileComponents($query)));
    }

    protected function compileComponents(Builder $query)
    {
        $sql = [];
        // 循环$selectComponents
        foreach ($this->selectComponents as $component) {
            // 如果Builder实例中对应的函数曾经被调用,那意味着对应的语法非空
            if (!is_null($query->$component)) {
                $method = 'compile' . ucfirst($component);
                // 编译该语法并将之收入$sql
                $sql[$component] = $this->$method($query, $query->$component);
            }
        }
        // 返回$sql数组
        return $sql;
    }

    protected function compileDistinct(Builder $query, $distinct)
    {
        return $distinct ? 'select distinct' : 'select';
    }

    protected function compileLimit(Builder $query, $limit)
    {
        return "limit $limit";
    }

    protected function compileOffset(Builder $query, $offset)
    {
        return "offset $offset";
    }

    protected function compileColumns(Builder $query, $columns)
    {
        return implode(', ', $columns);
    }

    protected function compileFrom(Builder $query, $table)
    {
        return 'from ' . $table;
    }

    protected function compileJoins(Builder $query, $joins)
    {
        $sql = [];
        foreach ($joins as $join) {
            // 如果存在多个副键和主键
            if (is_array($join->foregin) && is_array($join->primary)) {
                $on = [];
                // 循环键的数量, 将之与对应的主键组合
                for ($i = 0; $i < sizeof($join->foregin); $i++)
                    $on[] = $join->foregin[$i] . " = " . $join->primary[$i];
                // 最后再将所有句子用and连接
                $on = implode(' and ', $on);
            } else {
                //反之, 直接连接即可
                $on = "$join->foregin = $join->primary";
            }
            // 附上join的类型和副表
            $sql[] = trim("{$join->type} join {$join->table} on $on");
        }

        // 连接再返回
        return implode(' ', $sql);
    }

    protected function compileWheres(Builder $query)
    {
        $sql = [];
        // 类似与compileComponents(), 循环Builder实例中的$wheres
        foreach ($query->wheres as $where) {
            // 根据不同的$type来进行编译
            $method = "where{$where['type']}";
            // 返回的部分语句先收入数组
            $sql[] = $where['boolean'] . ' ' . $this->$method($query, $where);
        }
        // 最后将$sql数组连接起来, 删掉最前面的and或or在返回
        return 'where ' . preg_replace('/and |or /i', '', implode(" ", $sql), 1);
    }

    protected function whereBasic(Builder $query, $where)
    {
        // 检测$where[column]是否存在小数点
        // 是, 就意味着前缀已经附带了表名
        // 否, 为之后的字段添上表名
        // 因为join存在副表, 所以部分$where可能有附带表名, 这时候就不用添加了
        $table = !preg_match('/\./', $where['column']) ? $query->from . "." : '';
        // 返回添上表名的字段,和表达式, 再一个问号
        // 为何使用问号而不是:变量名? 因为:变量名存在太多局限性,不能标点符号,不能数字开头
        return $table . $where['column'] . ' ' . $where['operator'] . ' ?';
    }

    protected function whereIn(Builder $query, $where)
    {
        // 检测$where[column]是否存在小数点, 同理whereBasic()
        $table = !preg_match('/\./', $where['column']) ? $query->from . "." : '';
        // 有多少个匹配值那就连接多少个问号
        $values = implode(', ', array_fill(0, sizeof($where['values']), '?'));
        // 返回where in 语句
        return $table . $where['column'] . ' in (' . $values . ')';
    }

    protected function whereNotIn(Builder $query, $where)
    {
        // 检测$where[column]是否存在小数点, 同理whereBasic()
        $table = !preg_match('/\./', $where['column']) ? $query->from . "." : '';
        // 有多少个匹配值那就连接多少个问号
        $values = implode(', ', array_fill(0, sizeof($where['values']), '?'));
        // 返回where not in 语句
        return $table . $where['column'] . ' not in (' . $values . ')';
    }

    protected function whereNull(Builder $query, $where)
    {
        // 检测$where[column]是否存在小数点, 同理whereBasic()
        $table = !preg_match('/\./', $where['column']) ? $query->from . "." : '';
        // 返回where is null 语句
        return $table . $where['column'] . ' is null';
    }

    protected function whereNotNull(Builder $query, $where)
    {
        // 检测$where[column]是否存在小数点, 同理whereBasic()
        $table = !preg_match('/\./', $where['column']) ? $query->from . "." : '';
        // 返回where is not null 语句
        return $table . $where['column'] . ' is not null';
    }

    protected function compileGroups(Builder $query, $groups)
    {
        // 连接$groups, 返回group by语句
        return 'group by ' . implode(', ', $groups);
    }

    protected function compileOrders(Builder $query, $orders) {
        // 连接每一个$order与其$direction, 然后返回order by语句
        return 'order by '.implode(', ', array_map(function ($order) {
                return $order['column'].' '.$order['direction'];
            }, $orders));
    }

}