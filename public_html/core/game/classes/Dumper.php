<?php


namespace core\game\classes;


use core\game\interfaces\IDumpable;
use core\game\interfaces\IDumper;


abstract class Dumper implements IDumper
{

    protected $data;

    public function setData(IDumpable $obj)
    {
        $this->data = $obj->getDump();
    }


}