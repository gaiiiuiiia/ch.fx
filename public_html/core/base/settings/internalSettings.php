<?php


defined('ACCESS__') or die('Access denied');

use core\base\exceptions\RouteException;


const TEMPLATE = 'core/game/view/templates/';
const VIEW = 'core/game/view/';
const CSS_JS = [
    'styles' => [
        'core/game/view/style/style.css',
    ],
    'scripts' => [
        'core/game/view/js/functions.js',
        'core/game/view/js/scripts.js',
    ],
];


function autoloadMainClasses($class) {

    $class = str_replace('\\', '/', $class);

    if (!@include_once $class . '.php') {
        throw new RouteException('Не верное имя файла для подключения ' . $class);
    }

}

spl_autoload_register('autoloadMainClasses');
