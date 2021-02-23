<?php


namespace core\game\classes;


class Size implements \JsonSerializable
{
    protected $x;
    protected $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function jsonSerialize()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}