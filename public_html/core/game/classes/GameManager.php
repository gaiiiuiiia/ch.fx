<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\interfaces\IDumpable;
use core\game\model\Dumper;


class GameManager implements IDumpable
{

    use Singleton, GameManagerSubfunctions;

    protected $players;
    protected $map;

    protected $turnToMove;  // Игрок, чья очередь делать ход

    public function initGame(array $gameData)
    {

        $this->map = Map::getInstance();

        // изначально список обсталков пуст
        $this->map->init($gameData['mapSize'], $gameData['randomObst'] ? mt_rand(4, 7) : 0);

        $this->createPlayers($gameData['name'], $gameData['mapSize'], $gameData['amountObst']);

        $this->map->setPlayers($this->players);

        // установка очередности хода
        $this->setNextPlayerTurnToMove();

    }

    public function loadGame(int $matchID)
    {

        $gameData = Dumper::getInstance()->loadDataFromDB($matchID);

        $this->map = Map::getInstance();

        $this->loadPlayers($gameData['players']);
        $this->turnToMove = $gameData['turnToMove'];

        $this->map->init($gameData['size'], $gameData['obstacles']);

        $this->map->setPlayers($this->players);

        return json_encode($gameData);

    }

    public function get($property)
    {
        return self::getInstance()->$property;
    }

    private function createPlayers(string $name, Size $mapSize, int $amountObst)
    {

        $p1_pos = new Position((int)ceil($mapSize->getX() / 2), 1);
        $p2_pos = new Position((int)ceil($mapSize->getX() / 2), $mapSize->getY());

        $this->players[] = new Player1($name, $p1_pos, $this->map, $amountObst);
        $this->players[] = new Player1('Fox(AI)', $p2_pos, $this->map, $amountObst);

    }

    private function loadPlayers(array $data)
    {

        if ($data) {

            foreach ($data as $player) {
                $this->players[] = new Player1($player['name'], $player['position'], $this->map, $player['amountObstacles']);
            }

        } else {
            throw new GameException('Ошибка загрузки игроков');
        }

    }

    public function checkObstacle(array $newObstacle)
    {
        // проверка обстакла на корректность не наползает ли он куда и не закрывает ли игрока

        $allObstacles = $this->map->getObstacles();

        if ($allObstacles) {

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

    protected function setNextPlayerTurnToMove()
    {

        $this->turnToMove = $this->turnToMove
            ? ($this->turnToMove + 1) % count($this->players)
            : mt_rand(0, count($this->players) - 1);
    }

    public function getDump(): array
    {
        return [
            'players' => $this->players,
            'map' => $this->map,
            'turnToMove' => $this->turnToMove,
            'playerNames' => $this->getPlayerNames(' - '),
        ];
    }

    protected function saveDataToDB(int $matchID = null)
    {
        $data = $this->map->_getDump();
        $data['turnToMove'] = $this->turnToMove;
        $data['playerNames'] = $this->getPlayerNames(' - ');

        return Dumper::getInstance()->saveDataToDB($data, $matchID);
    }


}