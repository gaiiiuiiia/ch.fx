<?php


namespace core\game\classes;


class Size
{
    protected $x;
    protected $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function get($property) : int {
        return $this->$property;
    }
}