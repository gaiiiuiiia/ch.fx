<?php


namespace core\game\classes;


trait GameManagerSubfunctions
{

    public function getPlayerNames($glueString = false) {

        $names = [];

        if ($this->players) {
            foreach ($this->players as $player) {
                $names[] = $player->get('name');
            }
        }

        $names = $glueString ? implode($glueString, $names) : $names;

        return $names;
    }

}