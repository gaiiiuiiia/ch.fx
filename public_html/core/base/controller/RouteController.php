<?php


namespace core\base\controller;


use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class RouteController extends BaseController
{

    use Singleton, BaseMethods;

    protected $routes;

    private function __construct() {

        $address = $_SERVER['REQUEST_URI'];

        // отсечение GET-параметров
        if ($_SERVER['QUERY_STRING']){
            $address = substr($address, 0, strpos($address, $_SERVER['QUERY_STRING']) - 1);
        }

        // тут path это расположение файла index.php
        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));

        if ($path === PATH) {

            if (strrpos($address, '/') === strlen($address) - 1 &&
                strrpos($address, '/') !== strlen(PATH) - 1)
            {

                $this->redirect(rtrim($address, '/'), 301);
            }

            $this->routes = Settings::get('routes');

            if (!$this->routes){
                throw new RouteException('Отсутствуют маршруты в базовых настройках', 1);
            }

            $url = explode('/', substr($address, strlen(PATH)));

            $this->createRoute($url);

            $this->createParameters($url);

        }
        else {
            throw new RouteException('Некорректная дирректория сайта', 1);
        }

    }

    private function createRoute($url) {

        $route = [];

        $this->controller = $this->routes['game']['controller'];

        if (!empty($url[0])) {

            if ($this->routes['game']['routes'][$url[0]]) {
                $route = explode('/', $this->routes['game']['routes'][$url[0]]);
                $this->controller .= ucfirst($route[0] . 'Controller');
            }
            else {
                $this->controller .= ucfirst($url[0] . 'Controller');
            }
        }
        else {
            $this->controller .= $this->routes['default']['controller'];
        }

        $this->inputMethod = $route[1] ?: $this->routes['default']['inputMethod'];
        $this->outputMethod = $route[2] ?: $this->routes['default']['outputMethod'];
    }

    private function createParameters($url) {

        if ($url[1]){
            $key = '';

            for ($i = 1 ; $i < count($url); $i++){
                if (!$key){
                    $key = $url[$i];
                    $this->parameters[$key] = '';
                }else{
                    $this->parameters[$key] = $url[$i];
                    $key = '';
                }
            }
        }
    }

}