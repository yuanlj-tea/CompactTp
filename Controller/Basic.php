<?php
namespace CompactTp\Controller;

use Framework\Controller\Basic as FBasic;
use Framework\Lib\Response;
use CompactTp\Lib\Output;

class Basic extends FBasic
{
    protected $view;

    public function __construct()
    {
        $this->view=new \Framework\View\Basic();
    }

    /**
     * @param $name
     * @param array $data
     * @param bool $halt
     */
    public function render($name,array $data=array(),$halt=true)
    {
        empty($data) or $this->assign($data);
        $this->view->render($name,null);
        $halt && $this->halt();
    }

    /**
     * 中断脚本执行
     * @param string $info
     */
    public function halt($info='')
    {
        $this->assign(array(
            'buffer'=>ob_get_clean(),
            'info'=>$info,
        ));
        $this->display("public/error.html");
        exit(1);
    }

    /**
     * 封装Assign
     * @param $data
     */
    public function assign($data)
    {
        foreach($data as $key=>$value){
            Output::assign($key,$value);
        }
    }

    /**
     * 封装Display
     * @param $url
     */
    protected function display($url){
        Output::display($url);
    }

    protected function assign_batch_info($type,$batch='') {
        $type = empty($type) ? 0 : $type;
        if ($type) {
            $this->assign(array('type' => $type));
            $this->assign(array('batch' => $batch));
        }
        return true;
    }

    protected function ajaxExit($code,$msg='success',$data='') {
        header("Content-type: application/json,charset=utf-8");
        $returnInfo = array('code' => $code,'message' => $msg);
        if (is_array ( $data ) && count ( $data ) > 0) {
            $returnInfo = array_merge ( $returnInfo, $data );
        }
        echo json_encode($returnInfo);
        exit;
    }
}