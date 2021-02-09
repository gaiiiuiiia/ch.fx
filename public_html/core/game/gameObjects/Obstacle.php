<?php


namespace core\game\gameObjects;


class Obstacle
{

    protected $fromx;
    protected $fromy;
    protected $tox;
    protected $toy;

    public function __construct($fromx, $fromy, $tox, $toy) {
        $this->fromx = $fromx;
        $this->fromy = $fromy;
        $this->tox = $tox;
        $this->toy = $toy;
    }

    public function get($property) {
        return $this->$property;
    }

    public function _getDump() {
        return [
            'fromx' => $this->fromx,
            'fromy' => $this->fromy,
            'tox' => $this->tox,
            'toy' => $this->toy,
        ];
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

    static private function getNextPartOfObstacle($obstacle, $direction) {

        $fromx = $direction === 'row' ? $obstacle->get('fromx') + 1 : $obstacle->get('fromx');
        $fromy = $direction === 'col' ? $obstacle->get('fromy') + 1 : $obstacle->get('fromy');
        $tox   = $direction === 'row' ? $obstacle->get('tox')   + 1 : $obstacle->get('tox');
        $toy   = $direction === 'col' ? $obstacle->get('toy')   + 1 : $obstacle->get('toy');

        return new Obstacle($fromx, $fromy, $tox, $toy);

    }

}