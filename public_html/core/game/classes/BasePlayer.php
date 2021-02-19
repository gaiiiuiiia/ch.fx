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
    protected $moves;

    public function __construct(string $name, Position $position, IMap $map)
    {
        $this->name = $name;
        $this->position = $position;
        $this->map = $map;
        $this->setGoalRow();
        $this->setMoves();
    }

    abstract protected function setGoalRow();

    abstract protected function setMoves();


}