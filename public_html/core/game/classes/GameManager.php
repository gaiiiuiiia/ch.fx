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
    protected $winner;

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
        $this->winner = $gameData['winner'];
    }

    public function processMove(array $data): bool
    {
        $player = $this->getPlayerByName($data['playerName']);

        $newPos = new Position($data['moveData']['x'], $data['moveData']['y']);

        if ($this->canPlayerMove($player, $newPos)) {
            $player->move($newPos);
            if (!$this->hasWinner()) {
                $this->setNextPlayerTurnToMove();
            }
            return true;
        }

        return false;
    }

    private function canPlayerMove(BasePlayer $player, Position $position): bool
    {
        return $position->isSamePositionInArray($player->showMoves($player->getPosition(), false));
    }

    public function processObstacle(array $data): bool
    {
        $player = $this->getPlayerByName($data['playerName']);
        $playerAmountObstacles = $player->getAmountObstacles();

        if ($playerAmountObstacles > 0) {

            $newObst = [];

            foreach ($data['moveData'] as $obst) {
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

    /**
     * @param string $playerName
     * @return bool
     * @throws GameException
     * Метод просит игрока с именем $playerName сделать ход.
     * Игрок возвращает массив с двумя значениями: 1 - тип хода, 2 - данные хода
     * Тип может быть двух видов - move и obstacle.
     */
    public function letPlayerMove(string $playerName): bool
    {
        $player = $this->getPlayerByName($playerName);
        $moveData = $player->makeMove();

        if ($moveData) {

            switch ($moveData['type']) {
                case 'move':
                    $player->move($moveData['data']);
                    break;
                case 'obstacle':
                    $obstacles = $this->map->getObstacles();
                    $obstacles[] = $moveData['data'];
                    $this->map->setObstacles($obstacles);
                    $player->setAmountObstacles($player->getAmountObstacles() - 1);
                    break;
            }

            if (!$this->hasWinner()) {
                $this->setNextPlayerTurnToMove();
            }
            return true;
        } else {
            throw new GameException("Игрок $playerName не смог совершить ход...");
        }
    }

    protected function hasWinner(): bool
    {
        if ($this->winner) {
            return true;
        }
        else {
            foreach ($this->players as $player) {
                if ($this->checkWin($player)) {
                    $this->winner = $player->getName();
                    return true;
                }
            }
            return false;
        }
    }

    protected function checkWin(BasePlayer $player): bool
    {
        return $player->isOnGoalRow();
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
            foreach ($data as $playerData) {
                $pos_ = json_decode($playerData['position'], true);
                $player = new Player($playerData['name'], new Position($pos_['x'], $pos_['y']), $this->map, $playerData['amountObstacles']);
                $player->setGoalRow($playerData['goalRow']);
                $this->players[] = $player;
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
        if ($this->turnToMove) {
            if ($this->players) {
                foreach ($this->players as $player) {
                    if ($player->getName() === $this->turnToMove) {
                        $nextPlayerIndex = (array_search($player, $this->players) + 1) % count($this->players);
                        return $this->turnToMove = $this->players[$nextPlayerIndex]->getName();
                    }
                }
            }
        }

        return $this->turnToMove = $this->players[mt_rand(0, count($this->players) - 1)]->getName();
    }

    public function getDump(): array
    {
        return [
            'players' => $this->players,
            'map' => $this->map,
            'turnToMove' => $this->turnToMove,
            'playerNames' => $this->getPlayerNames(' - '),
            'winner' => $this->winner,
        ];
    }

}