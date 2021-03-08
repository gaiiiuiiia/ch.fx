<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\interfaces\IDumpable;


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
        $this->map->init($gameData['mapSize']);

        $this->createPlayers($gameData['name'], $gameData['mapSize'], $gameData['amountObst']);

        $this->map->setPlayers($this->players);

        if ($gameData['randomObst']) {
            $this->map->generateObstacles(mt_rand(3, 7));
        }

        // установка очередности хода
        $this->setNextPlayerTurnToMove();

    }

    public function loadGame(array $gameData)
    {
        $this->loadMap($gameData['map']);
        $this->loadPlayers($gameData['players']);
        $this->map->setPlayers($this->players);
        $this->turnToMove = $gameData['turnToMove'];
    }

    public function processMove(array $data): bool
    {
        $player = $this->getPlayerByName($data['playerName']);

        $playerPossibleMoves = $player->showMoves();

        $newPos = new Position($data['position']['x'], $data['position']['y']);

        if ($newPos->isSamePositionInArray($playerPossibleMoves)) {
            $player->setPosition($newPos);
            $this->setNextPlayerTurnToMove();
            return true;
        }

        return false;

    }

    public function processObstacle(array $data): bool
    {
        $player = $this->getPlayerByName($data['playerName']);
        $playerAmountObstacles = $player->getAmountObstacles();

        if ($playerAmountObstacles > 0) {

            $newObst = [];

            foreach ($data['obstacle'] as $obst) {
                $newObst[] = new Obstacle(
                    new Position($obst['from']['x'], $obst['from']['y']),
                    new Position($obst['to']['x'], $obst['to']['y'])
                );
            }

            if ($this->checkObstacle($newObst)) {
                $newObstacles = $this->map->getObstacles();
                $newObstacles[] = $newObst;
                $this->map->setObstacles($newObstacles);
            }

            $player->setAmountObstacles($playerAmountObstacles - 1);
            $this->setNextPlayerTurnToMove();

            return true;
        }

        return false;
    }

    private function createPlayers(string $name, Size $mapSize, int $amountObst)
    {
        $p1_pos = new Position((int)ceil($mapSize->getX() / 2), 1);
        $p2_pos = new Position((int)ceil($mapSize->getX() / 2), $mapSize->getY());

        $this->players[] = new Player($name, $p1_pos, $this->map, $amountObst);
        $this->players[] = new Player('Fox(AI)', $p2_pos, $this->map, $amountObst);
    }

    private function loadPlayers(array $data)
    {
        if ($data) {
            foreach ($data as $player) {
                $pos_ = json_decode($player['position'], true);
                $position = new Position($pos_['x'], $pos_['y']);
                $this->players[] = new Player($player['name'], $position, $this->map, $player['amountObstacles']);
            }
        } else {
            throw new GameException('Ошибка загрузки игроков');
        }
    }

    private function loadMap(array $data)
    {
        if ($data) {

            foreach ($data as $key => $value) {
                $data[$key] = json_decode($value, true);
            }

            $size = new Size($data['size']['x'], $data['size']['y']);

            $obstacles = [];
            foreach ($data['obstacles'] as $obs) {

                $obstacle = [];
                foreach ($obs as $part) {
                    $from_ = (array)json_decode($part['from'], true);
                    $from = new Position($from_['x'], $from_['y']);

                    $to_ = (array)json_decode($part['to'], true);
                    $to = new Position($to_['x'], $to_['y']);

                    $obstacle[] = new Obstacle($from, $to);
                }
                $obstacles[] = $obstacle;
            }

            $this->map = Map::getInstance();
            $this->map->init($size, $obstacles);
        }
    }

    public function getPossibleObstacles()
    {
        return $this->map->expandObstacles();
    }

    public function checkObstacle(array $obst)
    {
        // проверка обстакла на корректность не наползает ли он куда и не закрывает ли игрока

        $allObstacles = $this->map->getObstacles();

        if ($allObstacles) {

            // проверка препятствий на наложение друг на друга
            foreach ($allObstacles as $obst_) {
                if (Obstacle::isCollided($obst_, $obst)) {
                    return false;
                }
            }

            // проверка на наличие хода для игроков
            try {
                $newObstacles = $allObstacles;
                $newObstacles[] = $obst;
                $this->map->setObstacles($newObstacles);  // объединил два массива. оператор + не подходит

                foreach ($this->players as $player) {
                    if (!$player->getPathToFinish()) {
                        return false;
                    }
                }
            } catch (\Exception $e) {
                // описал такой проброс, чтобы выполнилось finally.
                // без него, в самом верху программы,
                // выполняется exit() и finally не выполняется
                throw $e;
            } finally {
                $this->map->setObstacles($allObstacles);
            }
        }
        return true;
    }

    protected function setNextPlayerTurnToMove()
    {
        $nextPlayerIndex = null;

        if ($this->turnToMove) {
            if ($this->players) {
                foreach ($this->players as $player) {
                    if ($player->getName() === $this->turnToMove) {
                        $nextPlayerIndex = intdiv(array_search($player, $this->players) + 1, 2);
                    }
                }
            }
        }
        $nextPlayerIndex = $nextPlayerIndex ?: mt_rand(0, count($this->players) - 1);

        $this->turnToMove = $this->players[$nextPlayerIndex]->getName();
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

}