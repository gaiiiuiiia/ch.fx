<?php


namespace core\game\classes;


use core\game\interfaces\IMap;
use core\game\interfaces\IPosition;
use core\game\libs\PathToRowFinder;

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
        $this->goalRow = $this->position->getY() === 1
            ? $this->map->getSizeY() : 1;
    }

    public function getGoalRow()
    {
        return $this->goalRow;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position)
    {
        $this->position = $position;
    }

    /**
     * @param IPosition|null $position
     * @param bool $ignoreOpponent
     * @return Position[] * the method returns a list of positions
     * that the player can make from the position.
     * If position is null, takes a current player position
     */
    public function showMoves(IPosition $position = null, bool $ignoreOpponent = false) : array
    {
        $position = $position ?: $this->position;

        $possibleMoves =  [
            new Position($position->getX() - 1, $position->getY()),
            new Position($position->getX(), $position->getY() - 1),
            new Position($position->getX() + 1, $position->getY()),
            new Position($position->getX(), $position->getY() + 1),
        ];

        foreach ($possibleMoves as $key => $move) {
            if (($move->isSamePosition($this->map->getOpponentPosition($this)) && !$ignoreOpponent)
                || $this->map->isMovePreventedByObstacle($position, $move)
                || !$this->map->isInBoard($move)) {

                unset($possibleMoves[$key]);
            }
        }

        return $possibleMoves;
    }

    public function getPathToFinish(): array
    {
        return (new PathToRowFinder($this))->findPath($this->position);
    }

    public function getAmountObstacles(): int
    {
        return $this->amountObstacles;
    }

    public function setAmountObstacles(int $amount)
    {
        $this->amountObstacles = $amount;
    }

    public function move(): Position
    {
        return new Position(1, 1);
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