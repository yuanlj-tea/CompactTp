<?php
namespace CompactTp\Model;

//use Framework\Data\Model;

class Message extends Model
{
    protected $table_name = 'message';

    public function getMsgById($id)
    {
        return $this->fetchAll(array('id'=>$id));
    }
}