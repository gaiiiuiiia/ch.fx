<?php


namespace core\game\classes;


use core\game\interfaces\IMap;
use core\game\interfaces\IPosition;
use core\game\libs\PathToRowFinder;

class Player extends BasePlayer implements \JsonSerializable
{

    protected $amountObstacles;

    public function __construct(string $name, Position $position, IMap $map, int $amountObstacles)
    {
        parent::__construct($name, $position, $map);
        $this->amountObstacles = $amountObstacles;
    }

    /**
     * @param IPosition|null $position
     * @param bool $ignoreOpponent
     * @return Position[] * the method returns a list of positions
     * that the player can make from the position.
     * If position is null, takes a current player position
     */
    public function showMoves(IPosition $position = null, bool $ignoreOpponent = false): array
    {
        $position = $position ?: $this->position;

        $possibleMoves = [
            new Position($position->getX() - 1, $position->getY()),
            new Position($position->getX(), $position->getY() - 1),
            new Position($position->getX() + 1, $position->getY()),
            new Position($position->getX(), $position->getY() + 1),
        ];

        foreach ($possibleMoves as $key => $move) {
            if (($move->isSamePosition($this->map->getOpponent($this)->getPosition()) && !$ignoreOpponent)
                || $this->map->isMovePreventedByObstacle($position, $move)
                || !$this->map->isInBoard($move)) {

                unset($possibleMoves[$key]);
            }
        }

        return $possibleMoves;
    }

    public function getPathToFinish(): array
    {
        return (new PathToRowFinder($this))->findPath($this->position);
    }

    public function getAmountObstacles(): int
    {
        return $this->amountObstacles;
    }

    public function setAmountObstacles(int $amount)
    {
        $this->amountObstacles = $amount;
    }

    public function move(Position $position): bool
    {
        $this->position = $position;
        return true;
    }

    public function makeMove()
    {
        if ($this->amountObstacles &&
            $obstacleToBlock = $this->getObstacleToBlockPlayer($this->map->getOpponent($this), 4)) {

            return ['type' => 'obstacle', 'data' => $obstacleToBlock];
        }
        else if ($possibleMoves = $this->showMoves()) {
            $path = $this->getPathToFinish();

            if ($path[0]->isSamePositionInArray($possibleMoves)) {
                $position = $path[0];
            }
            else {
                $anotherMovesLengthPathToFinish = [];
                foreach ($possibleMoves as $move) {
                    $anotherMovesLengthPathToFinish[array_search($move, $possibleMoves)]
                        = count((new PathToRowFinder($this))->findPath($move));
                }
                $shortPathLength = min($anotherMovesLengthPathToFinish);
                $position = $possibleMoves[array_search($shortPathLength, $anotherMovesLengthPathToFinish)];
            }

            return ['type' => 'move', 'data' => $position];
        }

        return false;
    }

    /**
     * метод возвращает объект Obstacle, если он увеличивает путь до финиша оппоненту не менее чем на $value единиц
     * @param Player $player
     * @param int $value - ценность препятствия
     * Результат может быть null
     * @return array
     */
    private function getObstacleToBlockPlayer(Player $player, int $value = 2)
    {
        $possibleObstaclesAroundPlayer = $this->getPossibleObstaclesAroundPlayer($player);

        if ($possibleObstaclesAroundPlayer) {

            $oldPath = $player->getPathToFinish();

            $oldObstacles = $this->map->getObstacles();

            foreach ($possibleObstaclesAroundPlayer as $obstacle) {

                try {
                    $newObstacles = $oldObstacles;
                    $newObstacles[] = $obstacle;
                    $this->map->setObstacles($newObstacles);  // объединил два массива. оператор + не подходит

                    $newPath = $player->getPathToFinish();

                    if (count($newPath) - count($oldPath) >= $value) {
                        return $obstacle;
                    }

                } catch (\Exception $e) {
                    // описал такой проброс, чтобы выполнилось finally.
                    // без него, в самом верху программы,
                    // выполняется exit() и finally не выполняется
                    throw $e;
                } finally {
                    $this->map->setObstacles($oldObstacles);
                }
            }
        }

        return null;
    }

    private function getPossibleObstaclesAroundPlayer(Player $player): array
    {

        $preventMoveObstacles = [];

        $playerMoves = $player->showMoves();

        if ($playerMoves) {

            $allPossibleObstacles = $this->map->expandObstacles();

            if ($allPossibleObstacles) {

                $playerPosition = $player->getPosition();

                foreach ($playerMoves as $possibleMove) {

                    foreach ($allPossibleObstacles as $obstacle) {

                        foreach ($obstacle as $part) {

                            if ($part->isMovePrevented($playerPosition, $possibleMove)) {
                                $preventMoveObstacles[] = $obstacle;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $preventMoveObstacles;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'position' => json_encode($this->position),
            'amountObstacles' => $this->amountObstacles,
            'goalRow' => $this->goalRow,
        ];
    }

}