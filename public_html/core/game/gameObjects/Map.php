<?php


namespace core\game\gameObjects;


use core\base\controller\Singleton;

class Map
{

    use Singleton;

    protected $size;
    protected $players;
    protected $obstacles;

    public function init($size, $obstacles, $players)
    {
        $this->size = $size;
        $this->obstacles = $obstacles;
        $this->players = $players;
    }

    public function get($property) {
        return self::getInstance()->$property;
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