<?php


namespace core\game\interfaces;

interface IMovable
{
    /**
     * @return array - ['x' => int, 'y' => int] - position on board
     */
    public function move() : array;

    /**
     * @return mixed
     * This method only shows how object can moving
     */
    public function showMoves() : array;

}