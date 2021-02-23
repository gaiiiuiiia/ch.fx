<?php


namespace core\game\classes;


use core\game\interfaces\IMap;
use core\game\interfaces\IPosition;

class Player extends BasePlayer implements \JsonSerializable
{

    protected $amountObstacles;

    public function __construct(string $name, Position $position, IMap $map, int $amountObstacles)
    {
        parent::__construct($name, $position, $map);
        $this->amountObstacles = $amountObstacles;
    }

    protected function setGoalRow()
    {
        $this->goalRow = $this->position->getX() === 1
            ? $this->map->getSizeY() : 1;
    }

    public function showMoves(IPosition $position = null) : array
    {

        $position = $position ?: $this->position;

        $possibleMoves =  [
            new Position($position->getX() - 1, $position->getY()),
            new Position($position->getX(), $position->getY() - 1),
            new Position($position->getX() + 1, $position->getY()),
            new Position($position->getX(), $position->getY() + 1),
        ];

        foreach ($possibleMoves as $key => $move) {
            if ($move === $this->map->getOpponentPosition($this)
                || $this->map->isMovePreventedByObstacle($position, $move)
                || !$this->map->isInBoard($move)) {

                unset($possibleMoves[$key]);
            }
        }

        return $possibleMoves;
    }

    public function move(): Position
    {
        return new Position(1, 1);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'position' => json_encode($this->position),
            'amountObstacles' => $this->amountObstacles,
        ];
    }

}