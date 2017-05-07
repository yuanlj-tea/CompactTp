<?php
namespace CompactTp\Controller;

use CompactTp\Model\Message;

class Home
{
    public function home()
    {
        header("Content-Type:text/html;charset=utf-8");
        $model=new Message();
        $msg=$model->getMsgById(7);
        p($msg,1);
    }
}