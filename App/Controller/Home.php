<?php
namespace App\Controller;

use App\Model\Message;

class Home
{
    public function home()
    {
        header("content-type:text/html;charset=utf-8");


        require (__DIR__.'/../Model/Message.php');
        $message=new Message();
        //print_r($message);
    }
}