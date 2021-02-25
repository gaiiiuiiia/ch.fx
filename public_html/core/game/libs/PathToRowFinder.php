<?php


namespace core\game\libs;


use core\base\exceptions\GameException;
use core\game\classes\Position;
use core\game\interfaces\IPosition;


class PathToRowFinder extends PathFinder
{
    /**
     * @param IPosition $position
     * @return array
     * ищет путь для $obj на $map с позиции $position до финишной черты
     */
    public function findPath(IPosition $position): array
    {
        $goalRow = $this->obj->getGoalRow();

        if ($position->getY() == $goalRow) {
            return [$position];
        }

        $iterLimit = 70;
        $usedPositions = [];  // пройденные точки ['cost' => [position, position, ...]]
        $deq = new \SplQueue();

        $current = ['position' => $position, 'cost' => 0, 'neighbor' => null];
        $usedPositions[$current['cost']][] = ['position' => $current['position'], 'neighbor' => $current['neighbor']];

        while ($iterLimit) {

            $newPositions = $this->obj->showMoves($current['position']);

            if ($newPositions) {

                foreach ($newPositions as $pos) {

                    if (!$this->inAllUsedPositions($pos, $usedPositions)) {
                        if ($pos->getY() === $goalRow) {
                            $usedPositions[$current['cost'] + 1][] = ['position' => $pos, 'neighbor' => $current['position']];
                            break 2;
                        }
                        else {
                            $usedPositions[$current['cost'] + 1][] = ['position' => $pos, 'neighbor' => $current['position']];
                        }
                        $deq->enqueue(['position' => $pos, 'cost' => $current['cost'] + 1, 'neighbor' => $current['position']]);
                    }
                }
            }
            if (!$deq->isEmpty()){
                $next = $deq->dequeue();
            }
            else {
                $iterLimit = 0;
                continue;
            }
            $current = ['position' => $next['position'], 'cost' => $next['cost'], 'neighbor' => $next['neighbor']];

            $iterLimit--;
        }

        if (!$iterLimit) {
            return [];
        }

        return $this->createPath($usedPositions);
    }

    /**
     * @param Position $pos
     * @param array $arr
     * @return bool true if given $pos in $arr
     */
    private function inAllUsedPositions(Position $pos, array $arr): bool
    {
        if ($arr) {
            foreach ($arr as $arr_)  {
                foreach ($arr_ as $item) {
                    if ($pos == $item['position']) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param array $usedPositions
     * @return array - path to goal row
     * this method constructs a path to goal row using a used positions
     */
    private function createPath(array $usedPositions): array
    {
        $path = [];
        $step = count($usedPositions) - 1;
        $startPosition = $usedPositions[0][0]['position'];
        $lastPosition = $usedPositions[$step][count($usedPositions[$step]) - 1];

        while ($lastPosition['position'] != $startPosition) {
            $path[] = $lastPosition['position'];
            $step--;
            // немного JS - описал функцию и тутже вызвал
            $lastPosition = (function (Position $pos, array $arr): array {
                foreach ($arr as $elem) {
                    if ($elem['position'] == $pos) {
                        return $elem;
                    }
                }
                throw new GameException('Некорректный путь до финиша');
            })($lastPosition['neighbor'], $usedPositions[$step]);  // вызов функции
        }

        $path[] = $startPosition;

        return array_reverse($path);
    }

}