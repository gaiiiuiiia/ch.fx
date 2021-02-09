<?php


namespace core\base\controller;


class BaseRoute
{
    use Singleton, BaseMethods;

    public static function routeDirection(){

        if (self::getInstance()->isAjax()){
            exit((new BaseAjax())->route());
        }

        RouteController::getInstance()->route();
    }
}