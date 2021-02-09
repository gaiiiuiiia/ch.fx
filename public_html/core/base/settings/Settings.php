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
                'new' => 'CreateGame/inputData/outputData',
            ],
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData',
        ],
    ];

    static public function get($property) {
        return self::getInstance()->$property;
    }

}