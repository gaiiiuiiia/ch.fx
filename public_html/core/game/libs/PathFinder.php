<?php


namespace core\game\libs;


use core\game\classes\BasePlayer;
use core\game\interfaces\IMap;
use core\game\interfaces\IPosition;

abstract class PathFinder
{

    protected $obj;

    public function __construct(BasePlayer $obj) {
        $this->obj = $obj;
    }

    public abstract function findPath(IPosition $position) : array;

}