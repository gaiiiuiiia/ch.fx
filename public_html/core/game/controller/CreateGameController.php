<?php


namespace core\game\controller;


class CreateGameController extends BaseGame
{

    protected function inputData() {

        $this->template = TEMPLATE . 'game';

        $this->execBase();

        if (!isset($_POST['start_game'])) {

            if ($this->matchID) {
                $this->gameManager->loadGame($this->matchID);
            }
            else {
                $this->redirect(PATH . 'new');
            }
        }
        else {

            // Обновили страничку - загружаем игру, а не создаем новую
            if ($this->matchID) {
                $this->gameManager->loadGame($this->matchID);
            }
            else {
                $this->createUserData();

                $this->matchID = $this->gameManager->initGame($this->userData);

                $_SESSION['match_id'] = $this->matchID;
            }
        }

        return [
            'size_x' => $this->gameManager->get('map')->get('size')['x'],
            'size_y' => $this->gameManager->get('map')->get('size')['y'],
            'playerNames' => $this->gameManager->getPlayerNames(' vs '),
        ];
    }

}