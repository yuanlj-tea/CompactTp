<?php
namespace App\Controller;

use Framework\Data\Model;

class Message extends Model
{
    protected $table = 'message';

    // 设置主键
    protected $identity = 'id';


}