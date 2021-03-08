<?php


namespace core\game\classes;


use core\base\exceptions\GameException;

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

    public function getPlayerByName(string $name): Player
    {
        foreach ($this->players as $player) {
            if ($player->getName() === $name) {
                return $player;
            }
        }

        throw new GameException('Не удалось найти игрока с именем ' . $name);
    }

}