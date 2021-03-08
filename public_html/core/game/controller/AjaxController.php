<?php


namespace core\game\controller;


use core\game\classes\DBDumper;
use core\game\classes\Loader;

class AjaxController extends BaseGame
{

    public function ajax() {

        if ($this->ajaxData['ajax']) {

            $this->execBase();

            foreach ($this->ajaxData as $key => $item) {
                $this->ajaxData[$key] = $this->clearStr($item);
            }

            switch ($this->ajaxData['ajax']) {

                case 'getGameData':

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    return json_encode($this->gameManager->getDump());

                    break;

                case 'getPossibleMoves':
                    $name = $this->ajaxData['name'];

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    foreach ($this->gameManager->getDump()['players'] as $player) {
                        if ($player->getName() === $name) {
                            $res = $player->showMoves();
                            sort($res);  // так в JS придет массив, а не связный список
                            return json_encode($res);
                        }
                    }

                    break;

                case 'getPossibleObstacles':

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    $obstacles = $this->gameManager->getPossibleObstacles();

                    return json_encode($obstacles);

                    break;

                case 'makeMove':

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    $moveData = json_decode($this->ajaxData['moveData'], true);

                    $data = [
                        'playerName' => $this->ajaxData['name'],
                    ];

                    switch ($moveData['type']) {
                        case 'obstacle':

                            $result = [];

                            $data['obstacle'] = array_slice($moveData, 1);

                            if ($this->gameManager->processObstacle($data)) {
                                (new DBDumper($this->gameManager))->saveDataToDB($this->matchID);
                                $result = $this->gameManager->getDump();
                                $result['status'] = 'ok';
                                $result['type'] = 'obstacle';
                            }
                            else {
                                $result['status'] = 'fail';
                            }

                            return json_encode($result);

                            break;

                        case 'move':
                            $data['position'] = array_slice($moveData, 1);
                            $this->gameManager->processMove($data);
                            break;
                    }

                    return json_encode($this->gameManager->getDump());

                    break;

                case 'endGame':

                    unset($_SESSION['match_id']);
                    unset($_SESSION['name']);

                    return PATH;

                    break;

            }



        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}