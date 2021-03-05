<?php


namespace core\game\controller;


use core\game\classes\DBDumper;
use core\game\classes\Loader;

class CreateGameController extends BaseGame
{

    protected function inputData() {

        $this->template = TEMPLATE . 'game';

        $this->execBase();

        if (!isset($_POST['startGame'])) {

            if ($this->matchID) {
                $gameData = (new Loader())->loadData($this->matchID);
                $this->gameManager->loadGame($gameData);
            }
            else {
                $this->redirect(PATH . 'new');
            }
        }
        else {

            // Обновили страничку - загружаем игру, а не создаем новую
            if ($this->matchID) {
                $gameData = (new Loader())->loadData($this->matchID);
                $this->gameManager->loadGame($gameData);
            }
            else {
                $this->createUserData();

                $this->gameManager->initGame($this->userData);

                $this->matchID = (new DBDumper($this->gameManager))->saveDataToDB();  // сохраняю данные в базу

                $_SESSION['match_id'] = $this->matchID;
                $_SESSION['name'] = $this->userData['name'];
            }
        }

        return [
            'size_x' => $this->gameManager->getMapSizeX(), // методы описаны в трейте GameManagerSubfunctions
            'size_y' => $this->gameManager->getMapSizeY(),
            'playerNames' => $this->gameManager->getPlayerNames(' vs '),
        ];
    }

}