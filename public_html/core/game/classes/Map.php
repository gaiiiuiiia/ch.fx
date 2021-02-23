<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\interfaces\IMap;
use core\game\interfaces\IMovable;
use core\game\interfaces\IPosition;


class Map extends BaseMap implements IMap, \JsonSerializable
{

    use Singleton;

    protected $players;
    protected $obstacles;

    /**
     * @param Size $size
     * @param int $amountRandomObstacles
     * @param array $obstacles - array of initial obstacles. WARNING!
     * it must be like [[Obstacle, Obstacle], ..., [Obstacle, Obstacle]]
     */
    public function init(Size $size, int $amountRandomObstacles = 0, array $obstacles = [])
    {
        $this->size = $size;

        $this->obstacles = $obstacles;

        if ($amountRandomObstacles > 0) {
            $this->generateObstacles($amountRandomObstacles);
        }
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

    private function generateObstacles(int $amount)
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

    public function get($property)
    {
        return self::getInstance()->$property;
    }

    /**
     * @param $player - Объект Player.
     * Метод возвращает позицию оппонента $player
     * @return false
     */
    public function getOpponentPosition(BasePlayer $player)
    {
        if (is_array($this->players) && $this->players) {
            foreach ($this->players as $_player) {
                if ($player !== $_player) {
                    return $_player->get('position');
                }
            }
        }

        return false;
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

    public function pathToFinish(IMovable $obj): array
    {
        return [];
    }

    public function getObstacles(): array
    {
        return $this->obstacles;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }
}