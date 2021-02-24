<?php


namespace core\game\interfaces;


use core\game\classes\Size;

interface IMap
{
    /**
     * @param IMovable $obj
     * @return array - array of positions
     * from $obj current position to finish line
     */
    public function pathToFinish(IMovable $obj) : array;

    public function getSize() : Size;

    public function getSizeY() : int;

    public function getSizeX() : int;

    public function getObstacles() : array;

    public function getPlayers() : array;

}