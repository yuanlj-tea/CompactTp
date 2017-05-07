<?php
namespace CompactTp\Model;

use CompactTp\Lib\Page;

class Model extends \Framework\Db\Model\Database
{
    protected $db_name=NULL;  //数据库名
    protected $db_options=array();
    protected $table_name='';   //表名
    protected $ds;

    /**
     * Model Instance Map
     *
     * @var array
     */
    protected static $instance = array();

    public function __construct($options = array()) {

        parent::__construct();

        //$this->ds = new \Office\Model\Cache();

        $options or $options = \Framework\Core\App::getOption('database');
        $this->db_options = $options;
        if ($this->db_name) {
            $this->db_options['dbname'] = $this->db_name;
        }
    }
    public function connection($options, $query = array()) {
        return parent::connection($this->db_options);
    }

    public function source($source, $data) {
        return $this->table_name;
    }
    /**
     * 获取分页数据
     * @param array $query
     * @param $url
     * @return array
     */
    public function fetchPage(Array $query,$url='') {
        $page = new Page($query);
        $queryLimit = $page->getLimit($query);
        $result = array();
        $result["data"] = $this->fetchAll($queryLimit);
        $totalNum = $this->fetchCount();
        $page->initTotalNum($totalNum);
        if ($url) {
            $result["pageMsg"] = $page->setPages($url);
        }
        $result["pageConf"] = $page->getPageConf();
        return $result;
    }
    /**
     * @param array $options
     * @return mixed
     */
    public static function model($options = array()) {
        $className = get_called_class();
        $instance  = & self::$instance[$className];
        if (is_null($instance)) {
            self::$instance[$className] = new $className($options);
        }
        return self::$instance[$className];
    }
}