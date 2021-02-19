<?php


namespace core\game\classes;


class Obstacle implements \JsonSerializable
{

    protected $from;
    protected $to;

    public function __construct(Position $from, Position $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function get($property)
    {
        return $this->$property;
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
        $obstacle_2 = Obstacle::getNextPartOfObstacle($obstacle_1, $direction);

        return [$obstacle_1, $obstacle_2];

    }

    /**
     * @param $obs1 - array of two Obstacle instances
     * @param $obs2 - array of two Obstacle instances
     * @return bool true if obstacles collides
     */
    static public function isCollided(array $obs1, array $obs2): bool
    {

        // obstacle is a array of two Obstacle objects
        // with 'fromx', 'fromy', 'tox', 'toy' props
        foreach ($obs1 as $part1) {
            foreach ($obs2 as $part2) {
                if ($part1 == $part2) {
                    return true;
                }
            }
        }

        return false;

    }

    static private function getNextPartOfObstacle(Obstacle $obstacle, string $direction): Obstacle
    {
        $from = new Position(
            $direction === 'row' ? $obstacle->get('from')->getX() + 1 : $obstacle->get('from')->getX(),
            $direction === 'col' ? $obstacle->get('from')->getY() + 1 : $obstacle->get('from')->getY()
        );
        $to = new Position(
            $direction === 'row' ? $obstacle->get('to')->getX() + 1 : $obstacle->get('to')->getX(),
            $direction === 'col' ? $obstacle->get('to')->getY() + 1 : $obstacle->get('to')->getY()
        );

        return new Obstacle($from, $to);

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