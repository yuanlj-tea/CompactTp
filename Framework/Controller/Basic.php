<?php
namespace Framework\Controller;

abstract class Basic
{
    public function __call($name,$args)
    {
        throw new \Framework\Core\Exception('Action'.$name.'not found',404);
        return false;
    }
}