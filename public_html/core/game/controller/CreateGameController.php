<?php


namespace core\game\controller;


class CreateGameController extends BaseGame
{

    protected $userData;

    protected function inputData() {

        $this->execBase();  // подгрузка стилей, получение модели

        $this->template = TEMPLATE . 'newgame';

        if (isset($_POST['start_game'])) {

            $this->createUserData();

            $this->gameManager->initGame($this->userData);

            $this->template = TEMPLATE . 'game';

        }

    }

    protected function createUserData() {

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if ($value) {
                    $this->userData[$key] = $value;
                }
            }
            $this->userData['size'] = $this->createMapSize($this->userData['size']);
        }

    }

    private function createMapSize($size) {

        $size = explode('x', $size);
        $x = $size[0];
        $y = $size[1];

        return compact('x', 'y');
    }

}