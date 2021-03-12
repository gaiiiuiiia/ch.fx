<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\interfaces\IMap;
use core\game\interfaces\IPosition;


class Map extends BaseMap implements IMap, \JsonSerializable
{

    use Singleton;

    protected $players;
    protected $obstacles;  // препятсвия, которые стоят на игровом поле
    protected $possibleObstacles;  // препятствия, которые можно поставить на игровое поле

    /**
     * @param Size $size
     * @param array $obstacles - array of initial obstacles. WARNING!
     * it must be like [[Obstacle, Obstacle], ..., [Obstacle, Obstacle]]
     */
    public function init(Size $size, array $obstacles = [])
    {
        $this->size = $size;
        $this->obstacles = $obstacles;
    }

    public function getObstacles(): array
    {
        return $this->obstacles;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setPlayers(array $listOfPlayers)
    {
        if (is_array($listOfPlayers) && $listOfPlayers) {
            foreach ($listOfPlayers as $player) {
                if (!($player instanceof BasePlayer)) {
                    throw new GameException('Неверно передан список игроков');
                }
            }
            $this->players = $listOfPlayers;
        }
    }

    public function setObstacles(array $obstacles)
    {
        if (is_array($obstacles) && $obstacles) {
            $this->obstacles = $obstacles;
        }
    }

    public function generateObstacles(int $amount)
    {
        $count = 0;
        while ($count < $amount) {

            $obstacle = Obstacle::getRandomObstacle($this->size);

            if (GameManager::getInstance()->checkObstacle($obstacle)) {
                $this->obstacles[] = $obstacle;
                $count++;
            }
        }

        return true;
    }

    public function expandObstacles(): array
    {

        if (!$this->possibleObstacles) {

            $allPossibleObstacles = [];
            for ($x = 1; $x < $this->getSizeX() ; $x++) {
                for ($y = 1; $y < $this->getSizeY(); $y++) {
                    // генерирую как горизонтальные, так и вертикальные части в одном цикле for
                    for ($i = 0; $i < 2; $i++) {
                        $part = new Obstacle(new Position($x, $y), new Position($x + $i, $y + 1 - $i));
                        $obstacle = [$part, Obstacle::getNextPartOfObstacle($part)];
                        if (GameManager::getInstance()->checkObstacle($obstacle)) {
                            $allPossibleObstacles[] = $obstacle;
                        }
                    }
                }
            }

            $this->possibleObstacles = $allPossibleObstacles;

        }

        return $this->possibleObstacles;
    }

    /**
     * @param $player - Объект Player.
     * Метод возвращает оппонента $player
     * @return Player
     * @throws GameException
     */
    public function getOpponent(BasePlayer $player): Player
    {
        if (is_array($this->players) && $this->players) {
            foreach ($this->players as $_player) {
                if ($player !== $_player) {
                    return $_player;
                }
            }
        }

        throw new GameException("Не удалось определить позицию оппонента для {$player->getName()}");

    }

    public function isMovePreventedByObstacle(IPosition $from, IPosition $to)
    {
        if (is_array($this->obstacles)) {

            foreach ($this->obstacles as $obstacle) {
                foreach ($obstacle as $part) {
                    if ($part->isMovePrevented($from, $to)) {
                        return true;
                    }
                }
            }

            return false;
        }

        throw new GameException('Некоректный вызов функции проверки препятствий');
    }

    public function isInBoard(IPosition $position)
    {
        return ($position->getX() > 0 && $position->getX() <= $this->size->getX())
            && ($position->getY() > 0 && $position->getY() <= $this->size->getY());
    }

    public function jsonSerialize()
    {
        return [
            'size' => json_encode($this->size),
            'obstacles' => json_encode($this->obstacles),
            'players' => json_encode($this->players),
        ];
    }
}