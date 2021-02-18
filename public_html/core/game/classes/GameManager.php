<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\model\Dumper;


class GameManager
{

    use Singleton, GameManagerSubfunctions;

    protected $players;
    protected $map;

    protected $turnToMove;  // Игрок, чья очередь делать ход

    public function initGame($gameData) {

        $this->map = Map::getInstance();

        // изначально список обсталков пуст
        $this->map->init($gameData['mapSize'], [], $gameData['randomObst'] ? mt_rand(4, 7) : 0);

        $this->createPlayers($gameData['name'], $gameData['mapSize'], $gameData['amountObst']);

        $this->map->setPlayers($this->players);

        // установка очередности хода
        $this->nextPlayerTurnToMove();

        return $this->saveDataToDB();

    }

    public function loadGame($matchID) {

        $gameData = Dumper::getInstance()->loadDataFromDB($matchID);

        $this->map = Map::getInstance();

        $this->loadPlayers($gameData['players']);
        $this->turnToMove = $gameData['turnToMove'];

        $this->map->init($gameData['size'], $gameData['obstacles']);

        $this->map->setPlayers($this->players);

        return json_encode($gameData);

    }

    public function get($property) {
        return self::getInstance()->$property;
    }

    private function createPlayers($name, $mapSize, $amountObst) {

        $p1_pos = [
            'x' => (int) ceil($mapSize['x'] / 2),
            'y' => 1,
        ];

        $p2_pos = [
            'x' => (int) ceil($mapSize['x']/ 2),
            'y' => $mapSize['y'],
        ];

        $this->players[] = new Player($name, $p1_pos, $amountObst, $this->map);
        $this->players[] = new Player('Fox(AI)', $p2_pos, $amountObst, $this->map);

    }

    private function loadPlayers($data) {

        if ($data && is_array($data)) {

            foreach ($data as $player) {
                $this->players[] = new Player($player['name'], $player['position'], $player['amountObstacles'], $this->map);
            }

        }
        else {
            throw new GameException('Ошибка загрузки игроков');
        }

    }

    public function checkObstacle($newObstacle) {
        // проверка обстакла на корректность не наползает ли он куда и не закрывает ли игрока

        $allObstacles = $this->map->get('obstacles');

        if (is_array($allObstacles) && $allObstacles) {

            // проверка препятствий на наложение друг на друга
            foreach ($allObstacles as $obstacle) {
                if (Obstacle::isCollided($obstacle, $newObstacle)) {
                    return false;
                }
            }

            // проверка на наличие хода для игроков
            /*foreach ($this->players as $player) {
                if (!$player->getPathToFinish($allObstacles + $newObstacle)) {
                    return false;
                }
            }*/

        }
        return true;
    }

    protected function nextPlayerTurnToMove() {

        $this->turnToMove = $this->turnToMove
            ? ($this->turnToMove + 1) % count($this->players)
            : mt_rand(0, count($this->players) - 1);
    }

    protected function saveDataToDB($matchID = false){
        $data = $this->map->_getDump();
        $data['turnToMove'] = $this->turnToMove;
        $data['playerNames'] = $this->getPlayerNames(' - ');

        return Dumper::getInstance()->saveDataToDB($data, $matchID);
    }


}