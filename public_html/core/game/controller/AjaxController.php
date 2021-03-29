<?php


namespace core\game\controller;


use core\game\classes\DBDumper;
use core\game\classes\Loader;

class AjaxController extends BaseGame
{

    public function ajax()
    {

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
                    $playerName = $this->ajaxData['name'];

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    foreach ($this->gameManager->getDump()['players'] as $player) {
                        if ($player->getName() === $playerName) {
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

                case 'processMove':

                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    $moveData = json_decode($this->ajaxData['moveData'], true);

                    $data = [
                        'playerName' => $this->ajaxData['name'],
                        'moveData' => array_slice($moveData, 1),
                    ];

                    $method = null;

                    switch ($moveData['type']) {
                        case 'obstacle':
                            $method  = "processObstacle";
                            break;

                        case 'move':
                            $method = 'processMove';
                            break;
                    }

                    if (method_exists($this->gameManager, $method) && $this->gameManager->$method($data)) {
                        (new DBDumper($this->gameManager))->saveDataToDB($this->matchID);
                        $result = $this->gameManager->getDump();
                        $result['status'] = 'ok';
                    }
                    else {
                        $result['status'] = 'fail';
                    }

                    return json_encode($result);
                    break;

                case 'makeMove':

                    $playerName = $this->ajaxData['name'];
                    $gameData = (new Loader())->loadData($this->matchID);
                    $this->gameManager->loadGame($gameData);

                    if ($this->gameManager->letPlayerMove($playerName)) {
                        (new DBDumper($this->gameManager))->saveDataToDB($this->matchID);
                        $result = $this->gameManager->getDump();
                        $result['status'] = 'ok';
                    } else {
                        $result['status'] = 'fail';
                    }

                    return json_encode($result);
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