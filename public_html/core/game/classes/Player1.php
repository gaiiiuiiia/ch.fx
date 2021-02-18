<?php


namespace core\game\classes;


class Player1 extends BasePlayer
{

    protected function setMoves()
    {
        $this->moves = [
            ['x' => $this->position['x'] - 1, 'y' => $this->position['y']],      // 1
            ['x' => $this->position['x'], 'y' => $this->position['y'] - 1],  // 2
            ['x' => $this->position['x'] + 1, 'y' => $this->position['y']],      // 3
            ['x' => $this->position['x'], 'y' => $this->position['y'] + 1],  // 4
        ];
    }

    protected function setGoalRow()
    {
        $this->goalRow = $this->position->getX() === 1
            ? $this->map->getSizeY() : 1;
    }

    public function move(): array
    {
        return [];
    }

    public function showMoves(): array
    {
        return $this->moves;
    }

}