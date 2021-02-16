<?php


namespace core\game\gameObjects;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;


class Map
{

    use Singleton;

    protected $size;
    protected $players;
    protected $obstacles;

    /**
     * @param $size
     * @param null $obstacles - initial obstacles, when the map will create
     */
    public function init($size, $obstacles = null)
    {
        $this->size = $size;
        $this->obstacles = $obstacles;
    }

    public function setPlayers($listOfPlayers) {

        if (is_array($listOfPlayers) && $listOfPlayers) {
            foreach ($listOfPlayers as $player) {
                if (!($player instanceof Player)) {
                    throw new GameException('Неверно передан список игроков');
                }
            }
            $this->players = $listOfPlayers;
        }
    }

    public function setObstacles($obstacles) {

        if (is_array($obstacles) && $obstacles) {
            $this->obstacles = $obstacles;
        }

    }

    public function generateObstacles($amount) {

        $count = 0;
        while ($count < $amount) {

            $obstacle = Obstacle::getRandomObstacle($this->size);

            if (GameManager::getInstance()->checkObstacle($obstacle)) {
                $this->obstacles[] = $obstacle;
                $count++;
            }
        }

        return true;
    }

    public function get($property) {
        return self::getInstance()->$property;
    }

    /**
     * @param $player - Объект Player.
     * Метод возвращает позицию оппонента $player
     */
    public function getOpponentPosition($player) {

        if (is_array($this->players) && $this->players) {
            foreach ($this->players as $_player) {
                if ($player !== $_player) {
                    return $_player->get('position');
                }
            }
        }

        return false;
    }

    public function isMovePreventedByObstacle($from, $to) {

        if (is_array($this->obstacles) && $this->obstacles) {

            foreach ($this->obstacles as $obstacle) {
                foreach ($obstacle as $part) {
                    if ($part->isMovePrevented($from, $to)) {
                        return true;
                    }
                }
            }

            return false;
        }

        throw new GameException('Некоректный вызов функции проверки препятствий');
    }

    public function isInBoard($position) {
        return ($position['x'] > 0 && $position['x'] <= $this->size['x'])
            && ($position['y'] > 0 && $position['y'] <= $this->size['y']);
    }

    public function _getDump() {
        return [
            'size' => $this->size,
            'obstacles' => $this->_getObstaclesDump(),
            'players' => $this->_getPlayersDump(),
        ];
    }

    private function _getObstaclesDump() {

        $obstaclesDumpList = [];

        if ($this->obstacles) {
            foreach ($this->obstacles as $obstacle) {
                $obstacleDump = [];
                foreach ($obstacle as $part) {
                    $obstacleDump[] = $part->_getDump();
                }
                $obstaclesDumpList[] = $obstacleDump;
            }
        }

        return $obstaclesDumpList;
    }

    private function _getPlayersDump() {

        $playersDumpList = [];

        if ($this->players) {
            foreach ($this->players as $player) {
                $playersDumpList[] = $player->_getDump();
            }
        }

        return $playersDumpList;
    }

}