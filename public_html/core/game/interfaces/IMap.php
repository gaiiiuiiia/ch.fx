<?php


namespace core\game\interfaces;


use core\game\classes\Size;

interface IMap
{

    public function getSize() : Size;

    public function getSizeY() : int;

    public function getSizeX() : int;

    public function getObstacles() : array;

    public function getPlayers() : array;

}