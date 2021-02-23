<?php


namespace core\game\classes;


use core\game\model\Model;

abstract class DataManager
{

    protected $model;

    public function __construct()
    {
        $this->model = Model::getInstance();
    }

}