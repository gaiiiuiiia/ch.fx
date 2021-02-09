<?php


namespace core\game\gameObjects;


use core\base\controller\Singleton;
use core\game\model\Model;


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

        $initialObstacles = $gameData['random_obst'] ? $this->generateObstacles(mt_rand(4, 7), $gameData['size']) : [];
        $this->createPlayers($gameData['name'], $gameData['size'], $gameData['amount_obst']);

        $this->map->init($gameData['size'], $initialObstacles, $this->players);

        foreach ($this->players as $player) {
            $player->__setMap($this->map);
        }

        // установка очередности хода
        $this->nextPlayerTurnToMove();

        $this->saveDataToDB();



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

    private function generateObstacles($amount, $mapSize) {

        $obstacles = [];

        while (count($obstacles) < $amount) {

            $obstacle = Obstacle::getRandomObstacle($mapSize);

            if ($this->checkObstacle($obstacle)) {
                $obstacles[] = $obstacle;
            }

        }

        return $obstacles;

    }

    private function checkObstacle($obstacle) {
        // проверка обстакла на корректность не наползает ли он куда и не закрывает ли игрока
        return true;
    }

    protected function nextPlayerTurnToMove() {

        $this->turnToMove = $this->turnToMove
            ? ($this->turnToMove + 1) % count($this->players)
            : mt_rand(0, count($this->players) - 1);
    }

    /**
     * @param false $matchID
     *
     * $map->_getDump() returns:
        'size' => ['x' => int, 'y' => int]
        'obstacles' => [
                       [[fromx, fromy, tox, toy], [fromx, fromy, tox, toy]],
                       ...
                       [[fromx, fromy, tox, toy], [fromx, fromy, tox, toy]]
        ]
        'players' => [['name' => ...,
                       'position' => ['x' => int, 'y' => int],
                       'amountObstacles' => int],

                       ['name' => ...,
                       'position' => ['x' => int, 'y' => int],
                       'amountObstacles' => int]
                       ]

     */
    protected function saveDataToDB($matchID = false) {

        $matchID = $matchID ?:
            Model::getInstance()->add('matches', [
                'fields' => [
                    'date' => 'NOW()',
                    'players' => $this->getPlayerNames(' - '),
                ],
                'return_id' => true,
            ]);

        $dumpData = $this->map->_getDump();
        $dumpData['turnToMove'] = $this->turnToMove;

        Model::getInstance()->add('match_logs', [
            'fields' => [
                'match_id' => $matchID,
                'state' => json_encode($dumpData),
            ],
        ]);
    }


}