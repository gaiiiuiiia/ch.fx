<?php


namespace core\game\interfaces;

use core\game\classes\Position;

interface IMovable
{

    public function move() : Position;

    /**
     * @return mixed
     * This method only shows how object can moving
     */
    public function showMoves() : array;

}