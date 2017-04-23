<?php
namespace App\Controller;

use App\Controller\Message;

class Home
{
    public function home()
    {
        header("content-type:text/html;charset=utf-8");
        /*$a=new Connector($GLOBALS['config']['database']);
        $bindValues = [
            'id'=>90,
            'columnInt'=>20,
        ];
        p($a->read('select * from a where id = :id and columnInt = :columnInt', $bindValues));*/

        $a = Message::select('msg', 'sender')->first();
        p($a->msg,1);
    }
}