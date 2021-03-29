<?php


namespace core\game\classes;


use core\game\interfaces\IMap;
use core\game\interfaces\IMovable;

abstract class BasePlayer implements IMovable
{
    protected $name;
    protected $position;
    protected $goalRow;
    protected $map;

    public function __construct(string $name, Position $position, IMap $map)
    {
        $this->name = $name;
        $this->position = $position;
        $this->map = $map;
        $this->setGoalRow();
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setGoalRow(int $goalRow = null)
    {
        if (!$goalRow) {
            $this->goalRow = $this->position->getY() === 1
                ? $this->map->getSizeY() : 1;
        }
        else {
            $this->goalRow = $goalRow;
        }
    }

    abstract public function isOnGoalRow(): bool;

}