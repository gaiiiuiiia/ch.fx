<?php


namespace core\game\classes;


class Player extends BasePlayer implements \JsonSerializable
{

    protected $name;
    protected $position;
    protected $goalRow;
    protected $amountObstacles;
    protected $map;

    protected $moves;

    /**
     * Player constructor.
     * @param String $name
     * @param array $position
     * @param int $amountObstacles
     * @param Map $map
     */
    public function __construct(string $name, array $position, int $amountObstacles, Map $map)
    {
        $this->name = $name;
        $this->position = $position;
        $this->amountObstacles = $amountObstacles;
        $this->map = $map;
        $this->setMoves();
        $this->goalRow = $this->position['y'] === 1 ? $map->get('size')['y'] : 1;
    }

    protected function setMoves()
    {
        $this->moves = [
            ['x' => $this->position['x'] - 1, 'y' => $this->position['y']],      // 1
            ['x' => $this->position['x'], 'y' => $this->position['y'] - 1],  // 2
            ['x' => $this->position['x'] + 1, 'y' => $this->position['y']],      // 3
            ['x' => $this->position['x'], 'y' => $this->position['y'] + 1],  // 4
        ];
    }

    public function showMoves() : array
    {
        return $this->moves;
    }

    public function move() : array
    {
        return [];
    }

    public function get(string $property)
    {
        return $this->$property;
    }

    /**
     * @param $obstacles - list of Obstacle instances
     * Method return a path to finish for current Player.
     * if $obstacles are given, path will be find using given obstacles
     */
    public function getPathToFinish(array $obstacles = [])
    {

        if ($obstacles) {

            $currentObstacles = $this->map->get('obstacles');

            try {
                $this->map->setObstacles($obstacles);

                $pathToFinish =

                    // создал очередь для возможных позиций. изначально в ней только позиция игрока
                $steps = new \SplQueue();
                $steps->enqueue($this->position);

                $iterLimit = 30;
                $currentPosition = $steps->dequeue();
                while ($iterLimit > 0 && !$this->isOnGoalRow($currentPosition)) {

                    /*foreach ($this->getPossibleMoves($currentPosition) as $newPosition) {
                        $steps->enqueue($newPosition);
                    }
                    */

                    $iterLimit--;
                }
            } catch (\Exception $e) {

            } finally {
                $this->map->setObstacles($currentObstacles);
            }


            $this->getPossibleMoves($this->position);

        } else {

        }

    }

    protected function getPossibleMoves(array $position)
    {

        /**
         * позиции "плюсик", куда в теории можно сходить из точки $position
         * 0 2 0
         * 1 P 3
         * 0 4 0
         */
        $possibleMoves = [
            ['x' => $position['x'] - 1, 'y' => $position['y']],      // 1
            ['x' => $position['x'], 'y' => $position['y'] - 1],  // 2
            ['x' => $position['x'] + 1, 'y' => $position['y']],      // 3
            ['x' => $position['x'], 'y' => $position['y'] + 1],  // 4
        ];

        foreach ($possibleMoves as $key => $move) {
            if ($move === $this->map->getOpponentPosition($this)
                || $this->map->isMovePreventedByObstacle($position, $move)
                || !$this->map->isInBoard($move)) {

                unset($possibleMoves[$key]);
            }
        }

        return $possibleMoves;
    }

    private function isOnGoalRow(array $position)
    {
        return $position['y'] === $this->goalRow;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'position' => $this->position,
            'amountObstacles' => $this->amountObstacles,
            'goalRow' => $this->goalRow,
        ];
    }

    public function _getDump()
    {
        return [
            'name' => $this->name,
            'position' => $this->position,
            'amountObstacles' => $this->amountObstacles,
            'goalRow' => $this->goalRow,
        ];
    }


}