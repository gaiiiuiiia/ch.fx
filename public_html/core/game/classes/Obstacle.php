<?php


namespace core\game\classes;


class Obstacle implements \JsonSerializable
{

    protected $from;
    protected $to;

    public function __construct(Position $from, Position $to) {
        $this->from = $from;
        $this->to = $to;
    }

    public function get($property) {
        return $this->$property;
    }

    public function jsonSerialize()
    {
        return [
            'from' => json_encode($this->from),
            'to' => json_encode($this->to),
        ];
    }

    public function _getDump() {

    }

    static public function getRandomObstacle($mapSize) {

        $direction = mt_rand(0, 1) ? 'row' : 'col';

        $fromx = mt_rand(1, $mapSize['x'] - 1);
        $fromy = mt_rand(1, $mapSize['y'] - 1);

        $tox = $direction === 'row' ? $fromx : $fromx + 1;
        $toy = $direction === 'col' ? $fromy : $fromy + 1;

        $obstacle_1 = new Obstacle($fromx, $fromy, $tox, $toy);
        $obstacle_2 = Obstacle::getNextPartOfObstacle($obstacle_1, $direction);

        return [$obstacle_1, $obstacle_2];

    }

    /**
     * @param $obs1 - array of two Obstacle instances
     * @param $obs2
     * @return bool true if obstacles collides
     */
    static public function isCollided($obs1, $obs2) {

        // obstacle is a array of two Obstacle objects
        // with 'fromx', 'fromy', 'tox', 'toy' props
        if (is_array($obs1) && is_array($obs2)) {

            foreach ($obs1 as $part1){
                foreach ($obs2 as $part2) {
                    if ($part1 == $part2) {
                        return true;
                    }
                }
            }

            return false;

        }

    }

    static private function getNextPartOfObstacle($obstacle, $direction) {

        $fromx = $direction === 'row' ? $obstacle->get('fromx') + 1 : $obstacle->get('fromx');
        $fromy = $direction === 'col' ? $obstacle->get('fromy') + 1 : $obstacle->get('fromy');
        $tox   = $direction === 'row' ? $obstacle->get('tox')   + 1 : $obstacle->get('tox');
        $toy   = $direction === 'col' ? $obstacle->get('toy')   + 1 : $obstacle->get('toy');

        return new Obstacle($fromx, $fromy, $tox, $toy);

    }

    public function isMovePrevented($from, $to) {

        if (   ($this->fromx === $from['x'] && $this->fromy === $from['y'] && $this->tox === $to['x'] && $this->toy === $to['y'])
            || ($this->fromx === $to['x'] && $this->fromy) === $to['y'] && $this->tox === $from['x'] && $this->toy === $from['y']) {
            return true;
        }

        return false;

    }

}