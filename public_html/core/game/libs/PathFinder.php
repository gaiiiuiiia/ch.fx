<?php


namespace core\game\libs;


use core\game\classes\Map;
use core\game\interfaces\IMovable;

abstract class PathFinder
{

    protected $map;
    protected $obj;

    public function __construct(IMovable $obj, Map $map) {
        $this->map = $map;
        $this->obj = $obj;
    }

    public abstract function findPath(array $position) : array;

}