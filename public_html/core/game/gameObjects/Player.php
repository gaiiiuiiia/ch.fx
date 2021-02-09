<?php


namespace core\game\gameObjects;


class Player
{

    protected $name;
    protected $position;
    protected $amountObstacles;
    protected $map;

    /**
     * Player constructor.
     * @param $name
     * @param $position
     * @param $amountObstacles
     * @param false $map - $map must be initialized after players
     */
    public function __construct($name, $position, $amountObstacles, $map = false) {

        $this->name = $name;
        $this->position = $position;
        $this->amountObstacles = $amountObstacles;
        $this->map = false;
    }

    public function __setMap($map) {
        $this->map = $map;
    }

    public function get($property) {
        return $this->$property;
    }

    public function _getDump() {
        return [
            'name' => $this->name,
            'position' => $this->position,
            'amountObstacles' => $this->amountObstacles
        ];
    }

}