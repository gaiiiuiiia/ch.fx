<?php


namespace core\game\classes;


use core\base\exceptions\GameException;

class Obstacle implements \JsonSerializable
{

    protected $from;
    protected $to;

    public function __construct(Position $from, Position $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): Position
    {
        return $this->from;
    }

    public function getTo(): Position
    {
        return $this->to;
    }

    public function __toString()
    {
        return "({$this->from->getX()}, {$this->from->getY()}) -> ({$this->to->getX()}, {$this->to->getY()})";
    }

    public function jsonSerialize()
    {
        return [
            'from' => json_encode($this->from),
            'to' => json_encode($this->to),
        ];
    }

    static public function getRandomObstacle(Size $mapSize): array
    {
        $direction = mt_rand(0, 1) ? 'row' : 'col';

        $from = new Position(
            mt_rand(1, $mapSize->getX() - 1),
            mt_rand(1, $mapSize->getY() - 1)
        );

        $to = new Position(
            $direction === 'row' ? $from->getX() : $from->getX() + 1,
            $direction === 'col' ? $from->getY() : $from->getY() + 1
        );

        $obstacle_1 = new Obstacle($from, $to);
        $obstacle_2 = Obstacle::getNextPartOfObstacle($obstacle_1);

        return [$obstacle_1, $obstacle_2];
    }

    /**
     * @param $obs1 - array of two Obstacle instances
     * @param $obs2 - array of two Obstacle instances
     * @return bool true if obstacles collides
     */
    static public function isCollided(array $obs1, array $obs2): bool
    {
        foreach ($obs1 as $part1) {
            foreach ($obs2 as $part2) {
                if ($part1 == $part2) {
                    return true;
                }
            }
        }
        return false;
    }

    static public function getNextPartOfObstacle(Obstacle $obstacle): Obstacle
    {
        if ($obstacle->getFrom()->getY() == $obstacle->getTo()->getY()) {
            return new Obstacle(
                new Position($obstacle->getFrom()->getX(), $obstacle->getFrom()->getY() + 1),
                new Position($obstacle->getTo()->getX(), $obstacle->getTo()->getY() + 1)
            );
        }
        else if ($obstacle->getFrom()->getX() == $obstacle->getTo()->getX()) {
            return new Obstacle(
                new Position($obstacle->getFrom()->getX() + 1, $obstacle->getFrom()->getY()),
                new Position($obstacle->getTo()->getX() + 1, $obstacle->getTo()->getY())
            );
        }
        throw new GameException("Некорретное препятствие $obstacle, чтобы определить его следущую часть...");
    }

    public function isMovePrevented(Position $from, Position $to): bool
    {
        if (($this->from->isSamePosition($from) && $this->to->isSamePosition($to))
            || ($this->from->isSamePosition($to)) && $this->to->isSamePosition($from)) {
            return true;
        }

        return false;
    }

}