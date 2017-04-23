<?php
namespace Framework\Data;

class Connector
{
    private $driver = 'mysql';
    private $host = 'localhost';
    private static $db = 'test';
    private $username = 'root';
    private $password = 'root';
    private $charset = 'utf8';
    protected $connection;
    protected static $container = [];

    protected $options = [
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    public function __construct($config = [])
    {
        self::initParam($config);
        if (empty(self::$container[self::$db])) {
            $this->connect();
        }
        $this->connection = self::$container[self::$db];
    }

    /**
     * 初始化连接参数
     * @param $config
     */
    public function initParam($config)
    {
        $this->driver = isset($config['driver']) ? $config['driver'] : '';
        $this->host = isset($config['host']) ? $config['host'] : '';
        $this->port = isset($config['port']) ? $config['port'] : '3306';
        $this->username = isset($config['user']) ? $config['user'] : '';
        $this->password = isset($config['pwd']) ? $config['pwd'] : '';
        $this->dbname = isset($config['dbname']) ? $config['dbname'] : '';
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';
    }

    public function buildConnectString()
    {
        return $this->driver . ':host=' . $this->host . ';dbname=' . self::$db;
    }

    public function connect()
    {
        try {
            // 连接数据库，生成\PDO实例， 将之赋予$connection，并存入$container之中
            self::$container[self::$db] = $this->connection = new \PDO($this->buildConnectString(), $this->username, $this->password, $this->options);
            // 返回数据库连接
            return $this->connection;
        } catch (\Exception $e) {
            p($e->getMessage());
        }
    }

    // 切换数据库
    public function setDatabase($db)
    {
        self::$db = $db;
        return $this;
    }

    /**
     * 读取数据
     * @param $sql
     * @param $bindings
     * @return mixed
     */
    public function read($sql, $bindings)
    {
        // 将sql语句放入预处理函数
        // $sql = select * from actor where first_name = ? and last_name = ?
        $statement = $this->connection->prepare($sql);
        // 将附带参数带入\PDO实例

        $this->bindValues($statement, $bindings);
        // 执行
        $statement->execute();
        // 返回所有合法数据, 以Object对象为数据类型
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * 绑定参数
     * @param $statement
     * @param $bindings
     */
    public function bindValues($statement, $bindings)
    {
        // $bindings = ['PENELOPE', 'GUINESS']
        // 依次循环每一个参数
        foreach ($bindings as $key => $value) {
            // $key = 0/1
            // $value = 'PENELOPE'/'GUINESS'
            $statement->bindValue(
            // 如果是字符串类型, 那就直接使用, 反之是数字, 将其+1
            // 这里是数值, 因此返回1/2
                is_string($key) ? $key : $key + 1,
                // 直接放入值
                // 'PENELOPE'/'GUINESS'
                $value,
                // 这里直白不多说
                // \PDO::PARAM_STR/\PDO::PARAM_STR
                is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR
            );
        }
    }

    /**
     * 更新数据
     * 与read不同的地方在于, read返回数据, update返回boolean(true/false)
     * @param $sql
     * @param $bindings
     * @return mixed
     */
    public function update($sql, $bindings)
    {
        $statement = $this->connection->prepare($sql);
        $this->bindValues($statement, $bindings);
        return $statement->execute();
    }

    // 与update一样, 分开是因为方便日后维护制定
    public function delete($sql, $bindings)
    {
        $statement = $this->connection->prepare($sql);
        $this->bindValues($statement, $bindings);
        return $statement->execute();
    }

    // 返回最新的自增ID, 如果有
    public function create($sql, $bindings)
    {
        $statement = $this->connection->prepare($sql);
        $this->bindValues($statement, $bindings);
        $statement->execute();
        return $this->lastInsertId();
    }

    // pdo自带,只是稍微封装
    public function lastInsertId()
    {
        $id = $this->connection->lastInsertId();
        return empty($id) ? null : $id;
    }

    public function exec($sql)
    {
        return $this->connection->exec($sql);
    }

    public function query($sql)
    {
        $q = $this->connection->query($sql);
        return $q->fetchAll(PDO::FETCH_OBJ);
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
        return $this;
    }

    public function rollBack()
    {
        $this->connection->rollBack();
        return $this;
    }

    public function commit()
    {
        $this->connection->commit();
        return $this;
    }

    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }
}
