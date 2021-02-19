<?php


namespace core\game\classes;


use core\game\model\Model;

class DBDumper extends Dumper
{
    protected $model;

    public function __construct()
    {
        $this->model = Model::getInstance();
    }

    public function dump()
    {
        // остановился здесь
        foreach ($this->data as $prop => $value) {
            $a = $prop;
        }

        $this->write();
    }

    protected function write()
    {

    }

}