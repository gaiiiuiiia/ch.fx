<?php


namespace core\game\controller;


class CreateGameController extends BaseGame
{

    protected function inputData() {

        $this->execBase();  // подгрузка стилей, получение модели

        $this->template = TEMPLATE . 'newgame';

        if (isset($_POST['start_game'])) {

            $this->createUserData();

            $this->matchID = $this->gameManager->initGame($this->userData);

            $_SESSION['match_id'] = $this->matchID;

            $this->template = TEMPLATE . 'game';

            return [
                'p1' => $this->gameManager->get('players')[0],
                'p2' => $this->gameManager->get('players')[1],
                'size_x' => $this->gameManager->get('map')->get('size')['x'],
                'size_y' => $this->gameManager->get('map')->get('size')['y'],
                'players' => $this->gameManager->get('players'),
                'map' => $this->gameManager->get('map'),
                'turnToMove' => $this->gameManager->get('turnToMove'),
            ];
        }

    }

}