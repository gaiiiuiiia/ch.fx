<?php


namespace core\game\classes;


trait GameManagerSubfunctions
{

    public function getPlayerNames($glueString = false) {

        $names = [];

        if ($this->players) {
            foreach ($this->players as $player) {
                $names[] = $player->getName();
            }
        }

        $names = $glueString ? implode($glueString, $names) : $names;

        return $names;
    }

    public function getMapSizeX() :int
    {
        assert($this->map instanceof Map);
        return $this->map->getSizeX();
    }

    public function getMapSizeY() :int
    {
        assert($this->map instanceof Map);
        return $this->map->getSizeY();
    }

}