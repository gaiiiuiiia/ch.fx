<?php


namespace core\game\interfaces;

use core\game\classes\Position;

interface IMovable
{

    public function move() : Position;

    public function showMoves(IPosition $position) : array;

}