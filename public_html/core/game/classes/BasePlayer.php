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

    abstract protected function setGoalRow();

    abstract public function getGoalRow();

}