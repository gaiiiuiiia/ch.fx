<?php


namespace core\base\controller;


use core\base\settings\Settings;

class BaseAjax extends BaseController
{

    public function route() {

        $routes = Settings::get('routes');

        $controller = $routes['game']['controller'] . 'AjaxController';

        $controller = str_replace('/', '\\', $controller);

        $ajax = new $controller;

        $ajax->ajaxData = $this->isPost() ? $_POST : $_GET;

        return $ajax->ajax();

    }

}