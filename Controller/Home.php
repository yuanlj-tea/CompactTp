<?php
namespace CompactTp\Controller;

use CompactTp\Model\Message;
use Framework\Lib\Request;
class Home extends Basic
{
    public function home()
    {
        //p(Request::isAjax(),1);
        //p($_SERVER,1);
        header("Content-Type:text/html;charset=utf-8");
        $model=new Message();
        $msg=$model->getMsgById(7);
        p($msg,1);
    }

    public function view()
    {
        $data=['test'=>'test smarty view'];
        $this->assign($data);
        $this->display('home/view.html');
    }
}