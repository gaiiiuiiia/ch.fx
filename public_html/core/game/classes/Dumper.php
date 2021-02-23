<?php


namespace core\game\classes;


use core\game\interfaces\IDumpable;

abstract class Dumper extends DataManager
{

    protected $data;

    public function __construct(IDumpable $obj)
    {
        parent::__construct();
        $this->data = $obj->getDump();
    }

}