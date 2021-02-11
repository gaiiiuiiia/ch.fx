<?php


namespace core\game\controller;


use core\base\controller\BaseController;
use core\game\gameObjects\GameManager;
use core\game\model\Model;

abstract class BaseGame extends BaseController
{

    protected $userData;

    protected $matchID;

    protected $model;

    protected $gameManager;


    protected function inputData() {

        $this->init();

        $this->model = $this->model ?: Model::getInstance();
        $this->gameManager = $this->gameManager ?: GameManager::getInstance();

        $this->matchID = $_SESSION['match_id'] ?: false;

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

    protected function createUserData() {

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if ($value) {
                    $this->userData[$key] = $this->clearStr($value);
                }
            }
            $this->userData['mapSize'] = $this->createMapSize($this->userData['mapSize']);
        }

    }

    private function createMapSize($size) {

        $size = explode('x', $size);
        $x = $size[0];
        $y = $size[1];

        return compact('x', 'y');
    }


}