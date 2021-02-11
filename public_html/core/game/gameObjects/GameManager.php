<?php


namespace core\game\gameObjects;


use core\base\controller\Singleton;
use core\game\model\Dumper;


class GameManager
{

    use Singleton, GameManagerSubfunctions;

    protected $players;
    protected $map;

    protected $turnToMove;  // Игрок, чья очередь делать ход

    private function __construct() {
        $this->map = Map::getInstance();
    }

    public function initGame($gameData) {

        $this->createPlayers($gameData['name'], $gameData['mapSize'], $gameData['amount_obst']);

        $this->map->init($gameData['mapSize'], [], $this->players);

        if ($gameData['random_obst'])
            $this->map->generateObstacles(mt_rand(4, 7));

        foreach ($this->players as $player) {
            $player->__setMap($this->map);
        }

        // установка очередности хода
        $this->nextPlayerTurnToMove();

        return $this->saveDataToDB();

    }

    public function loadGame($matchID) {

        $gameData = Dumper::getInstance()->loadDataFromDB($matchID);

        return json_encode($gameData);

    }

    public function get($property) {
        return self::getInstance()->$property;
    }

    private function createPlayers($name, $mapSize, $amountObst) {

        $p1_pos = [
            'x' => intdiv($mapSize['x'], 2),
            'y' => 1,
        ];
        $p2_pos = [
            'x' => intdiv($mapSize['x'], 2),
            'y' => $mapSize['y'],
        ];
        $this->players[] = new Player($name, $p1_pos, $amountObst);
        $this->players[] = new Player('Fox(AI)', $p2_pos, $amountObst);

    }

    public function checkObstacle($obstacle) {
        // проверка обстакла на корректность не наползает ли он куда и не закрывает ли игрока
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