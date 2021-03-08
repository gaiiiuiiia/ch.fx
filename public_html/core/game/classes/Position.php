<?php


namespace core\game\classes;


use core\game\interfaces\IPosition;

class Position implements IPosition, \JsonSerializable
{

    protected $x;
    protected $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function isSamePosition(Position $pos) : bool
    {
        return $this->x === $pos->getX() && $this->y === $pos->getY();
    }

    public function isSamePositionInArray(array $arr): bool
    {
        if ($arr) {
            foreach ($arr as $pos) {
                if ($this->isSamePosition($pos)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function jsonSerialize()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }


}