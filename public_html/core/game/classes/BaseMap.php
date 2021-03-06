<?php


namespace core\game\classes;


abstract class BaseMap
{

    protected $size;

    public function getSize() : Size
    {
        return $this->size;
    }

    public function getSizeX() : int
    {
        return $this->size->getX();
    }

    public function getSizeY() : int
    {
        return $this->size->getY();
    }

}