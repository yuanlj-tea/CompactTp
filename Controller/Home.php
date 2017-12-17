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
        //p(Request::get('test','默认数据'),1);
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