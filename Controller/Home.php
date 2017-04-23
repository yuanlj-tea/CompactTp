<?php
namespace CompactTp\Controller;

use CompactTp\Model\Message;

class Home
{
    public function home()
    {
        header("Content-Type:text/html;charset=utf-8");
        $data=Message::get();
        p($data,1);
    }
}