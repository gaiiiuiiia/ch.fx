<?php


namespace core\game\controller;


use core\base\controller\BaseController;
use core\base\exceptions\GameException;
use core\base\settings\Settings;
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

        $validate = Settings::get('validate');

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if ($value) {
                    if ($validate[$key]) {
                        if ($this->validate($key, $value)) {
                            $this->userData[$key] = $this->clearStr($value);
                        }
                        else {
                            throw new GameException('Данные не прошли валидацию');
                        }
                    }
                    else {
                        $this->userData[$key] = $this->clearStr($value);
                    }
                }
            }
            $this->userData['mapSize'] = $this->createMapSize($this->userData['mapSize']);
        }

    }

    protected function validate($key, $value) {

        $validateParams = Settings::get('validate')[$key];

        if (is_array($validateParams) && $validateParams) {

            foreach ($validateParams as $param => $valParam) {
                switch ($param) {

                    case 'inGameSettings':
                        if ($valParam) {
                            $gameSettings = Settings::get('gameSettings')[$key];
                            if (in_array($value, $gameSettings)) {
                                continue 2;
                            }
                            return false;
                        }
                        break;
                }

            }

            return true;

        }
        else {
            throw new GameException('Некорректные валидационные параметры');
        }

    }

    private function createMapSize($size) {

        $size = explode('x', $size);
        $x = $this->clearNum($size[0]);
        $y = $this->clearNum($size[1]);

        return compact('x', 'y');
    }


}