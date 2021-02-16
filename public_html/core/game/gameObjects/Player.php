<?php


namespace core\game\gameObjects;


class Player
{

    protected $name;
    protected $position;
    protected $goalRow;
    protected $amountObstacles;
    protected $map;

    /**
     * Player constructor.
     * @param $name
     * @param $position
     * @param $amountObstacles
     * @param $map
     */
    public function __construct($name, $position, $amountObstacles, $map) {

        $this->name = $name;
        $this->position = $position;
        $this->amountObstacles = $amountObstacles;
        $this->map = $map;
        $this->goalRow = $this->setGoalRow();
    }

    public function get($property) {
        return $this->$property;
    }

    /**
     * @param false $obstacles - list of Obstacles
     * Method return a path to finish for current Player.
     * if $obstacles are given, path will be find using given obstacles
     */
    public function getPathToFinish($obstacles = false) {

        /**
         * как проверить существование пути
         * 1) получить список доступных сейчас ходов
         * 2) для каждого полученного хода получть доступные ходы.
         * исключать ходы обратно!
         * 3) если на каком-то шаге мы получим ячейку финишной черты, то завершаем цикл
         */

        if ($obstacles) {

            $currentObstacles = $this->map->get('obstacles');

            try {
                $this->map->setObstacles($obstacles);

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
            }
            catch (\Exception $e) {

            } finally {
                $this->map->setObstacles($currentObstacles);
            }


            $this->getPossibleMoves($this->position);

        }
        else {

        }

    }

    protected function getPossibleMoves($position) {

        /**
         * позиции "плюсик", куда в теории можно сходить из точки $position
         * 0 2 0
         * 1 P 3
         * 0 4 0
         */
        $possibleMoves = [
            ['x' => $position['x'] - 1, 'y' => $position['y']],      // 1
            ['x' => $position['x'],     'y' => $position['y'] - 1],  // 2
            ['x' => $position['x'] + 1, 'y' => $position['y']],      // 3
            ['x' => $position['x'],     'y' => $position['y'] + 1],  // 4
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

    private function isOnGoalRow($position) {
        return $position['y'] === $this->goalRow;
    }

    public function _getDump() {
        return [
            'name' => $this->name,
            'position' => $this->position,
            'amountObstacles' => $this->amountObstacles,
            'goalRow' => $this->goalRow,
        ];
    }

    private function setGoalRow() {

        if (!$this->goalRow) {
            return $this->position['y'] === 1 ? $this->map->get('size')['y'] : 1;
        }

        return $this->goalRow;
    }

}