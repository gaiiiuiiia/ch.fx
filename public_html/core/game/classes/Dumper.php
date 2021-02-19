<?php


namespace core\game\classes;


use core\game\interfaces\IDumpable;
use core\game\interfaces\IDumper;
use core\game\model\Model;

abstract class Dumper extends Model implements IDumper
{

    protected $data;

    public function setData(IDumpable $obj)
    {
        $this->data = $obj->getDump();
    }


}