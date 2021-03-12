<?php


namespace core\game\interfaces;

use core\game\classes\Position;

interface IMovable
{

    public function move(Position $position): bool;

    public function showMoves(IPosition $position, bool $ignoreOpponent) : array;

}