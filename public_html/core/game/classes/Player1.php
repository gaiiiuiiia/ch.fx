<?php


namespace core\game\classes;


use core\game\interfaces\IMap;

class Player1 extends BasePlayer implements \JsonSerializable
{

    protected $amountObstacles;

    public function __construct(string $name, Position $position, IMap $map, int $amountObstacles)
    {
        parent::__construct($name, $position, $map);
        $this->amountObstacles = $amountObstacles;
    }

    protected function setMoves()
    {
        $this->moves = [
            ['x' => $this->position->getX() - 1, 'y' => $this->position->getY()],      // 1
            ['x' => $this->position->getX(), 'y' => $this->position->getY() - 1],  // 2
            ['x' => $this->position->getX() + 1, 'y' => $this->position->getY()],      // 3
            ['x' => $this->position->getX(), 'y' => $this->position->getY() + 1],  // 4
        ];
    }

    protected function setGoalRow()
    {
        $this->goalRow = $this->position->getX() === 1
            ? $this->map->getSizeY() : 1;
    }

    public function move(): Position
    {
        return new Position(1, 1);
    }

    public function showMoves(): array
    {
        return $this->moves;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'position' => json_encode($this->position),
            'amountObstacles' => $this->amountObstacles,
            'goalRow' => $this->goalRow,
        ];
    }

}