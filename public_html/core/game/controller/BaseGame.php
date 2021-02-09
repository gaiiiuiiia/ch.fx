<?php


namespace core\game\controller;


use core\base\controller\BaseController;
use core\game\gameObjects\GameManager;
use core\game\model\Model;

abstract class BaseGame extends BaseController
{
    protected $model;

    protected $gameManager;


    protected function inputData() {

        $this->init();

        $this->model = $this->model ?: Model::getInstance();
        $this->gameManager = $this->gameManager ?: GameManager::getInstance();

    }

    protected function outputData() {

        if (!$this->content) {
            $args = func_get_arg(0);
            $vars = $args ?: [];
            $this->content = $this->render($this->template, $vars);
        }

        $this->header = $this->render(TEMPLATE . 'include/header');
        $this->footer = $this->render(TEMPLATE . 'include/footer');

        return($this->render(TEMPLATE . 'layout/default'));
    }

    protected function execBase() {
        self::inputData();
    }


}