<?php


namespace core\game\classes;


use core\game\interfaces\IMap;
use core\game\interfaces\IMovable;

abstract class BasePlayer implements IMovable
{
    protected $position;
    protected $goalRow;
    protected $map;
    protected $moves;

    public function __construct(Position $position, IMap $map)
    {
        $this->position = $position;
        $this->map = $map;
        $this->setGoalRow();
    }

    abstract protected function setGoalRow();

    abstract protected function setMoves();


}