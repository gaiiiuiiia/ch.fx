<?php


namespace core\game\classes;


use core\base\controller\Singleton;
use core\base\exceptions\GameException;
use core\game\interfaces\IMap;
use core\game\interfaces\IMovable;
use core\game\interfaces\IPosition;


class Map extends BaseMap implements IMap
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
    public function getOpponentPosition(Player $player)
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

    public function isMovePreventedByObstacle(array $from, array $to)
    {

        if (is_array($this->obstacles) && $this->obstacles) {

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

    public function isInBoard(array $position)
    {
        return ($position['x'] > 0 && $position['x'] <= $this->size['x'])
            && ($position['y'] > 0 && $position['y'] <= $this->size['y']);
    }

    public function _getDump()
    {
        return [
            'size' => $this->size,
            'obstacles' => $this->_getObstaclesDump(),
            'players' => $this->_getPlayersDump(),
        ];
    }

    private function _getObstaclesDump()
    {

        $obstaclesDumpList = [];

        if ($this->obstacles) {
            foreach ($this->obstacles as $obstacle) {
                $obstacleDump = [];
                foreach ($obstacle as $part) {
                    $obstacleDump[] = $part->_getDump();
                }
                $obstaclesDumpList[] = $obstacleDump;
            }
        }

        return $obstaclesDumpList;
    }

    private function _getPlayersDump()
    {

        $playersDumpList = [];

        if ($this->players) {
            foreach ($this->players as $player) {
                $playersDumpList[] = $player->_getDump();
            }
        }

        return $playersDumpList;
    }

    public function pathToFinish(IMovable $obj): array
    {
        return [];
    }

    /**
     * @param IPosition $position
     * @param IMovable $object
     * @return array
     * возвращает возможные ходы для $object с позиции $position
     */
    public function getPossibleMoves(IPosition $position, IMovable $object): array
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