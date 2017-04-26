<?php
namespace CompactTp\Model;

//use Framework\Data\Model;
use Framework\Think\Model;
class Message extends Model
{
    protected $table = 'message';

    protected $identity = 'id';

    public function __construct()
    {
        $a=D('test');
    }
}