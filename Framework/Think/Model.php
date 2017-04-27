<?php
namespace Framework\Think;

/**
 * Model模型类
 * 实现了ORM和ActiveRecords模式
 */
class Model
{
	//操作状态
	const MODEL_INSERT=1;		//插入模型数据
	const MODEL_UPDATE=2;		//更新模型数据
	const MODEL_BOTH=3;		//包含上面两种方式
	const MUST_VALIDATE=1;		//必须验证
	const EXISTS_VALIDATE=0;		//表单存在字段则验证
	const VALUE_VALIDATE=2;		//表单值不为空则验证

	//当前数据库操作对象
	protected $db=null;
	//数据库对象池
	protected $_db=array();
	//主键名称
	protected $pk='id';
	//主键是否自动增长
	protected $autoinc=false;
	//数据表前缀
	protected $tablePrefix=null;
	//模型名称
	protected $name='';
	//数据库名称
	protected $dbName='';
	//数据库配置
	protected $connection='';
	//数据表名（不包含表前缀）
	protected $tableName='';
	//实际数据表名
	protected $trueTableName='';
	//最近错误信息
	protected $error='';
	//字段信息
	protected $fields=array();
	//数据信息
	protected $data=array();
	//查询表达式参数
	protected $options=array();
	protected $_validate=array();	//自动验证定义
	protected $_auto=array();		//自动完成定义
	protected $_map=array();		//字段映射定义
	protected $_score=array();		//命名范围定义
	//是否自动检测数据表字段信息
	protected $autoCheckFields=true;
	//是否批处理验证
	protected $patchValidate=false;
	//链操作方法列表
	protected $methods=array('strict','order','alias','having','group','lock','distinct','auto','filter','validate','result','token','index','force');

	/**
	 * 架构函数
	 * 取得DB类的实例对象，字段检查
	 * @access public
	 * @param  string $name 模型名称
	 * @param  string $tablePrefix 表前缀
	 * @param  mixed $connection 数据库连接信息
	 */
	public function __construct($name='',$tablePrefix='',$connection='')
	{
		//模型初始化
		//$this->_initialize();
		//获取模型名称
        if(!empty($name)){
            if(strpos($name,'.')){  //支持数据库名.模型名的定义
                list($this->dbName,$this->name)=explode('.',$name);
            }else{
                $this->name=$name;
            }
        }elseif(empty($this->name)){
            $this->name=$this->getModelName();p($this->name,1);
        }
	}

    public function getModelName()
    {
        if(empty($this->name)){
            $name=substr(get_class($this),0,-strlen(C('DEFAULT_M_LAYER')));
            if($pos=strrpos($name,'\\')){   //有命名空间
                $this->name=substr($name,$pos+1);
            }else{
                $this->name=$name;
            }
        }
        return $this->name;
	}
}