<?php


namespace core\base\settings;


use core\base\controller\Singleton;

class Settings
{

    use Singleton;

    private $routes = [
        'base' => [
            'controller' => 'core/base/controller/',
            'settings' => 'ore/base/settings/',
        ],
        'game' => [
            'controller' => 'core/game/controller/',
            'routes' => [
                'new' => 'Newgame/inputData/outputData',
                'play' => 'CreateGame/inputData/outputData'
            ],
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData',
        ],
    ];

    private $validate = [
        'mapSize' => ['inGameSettings' => true],
        'amountObstacles' => ['inGameSettings' => true],
    ];

    private $gameSettings = [
        'mapSize' => ['5x5', '5x6', '5x7', '6x5', '6x7', '7x5', '7x9'],  // размеры игрового поля
        'amountObstacles' => [1, 2, 3],  // стартовый запас препятствий у игрока
    ];

    static public function get($property) {
        return self::getInstance()->$property;
    }

}