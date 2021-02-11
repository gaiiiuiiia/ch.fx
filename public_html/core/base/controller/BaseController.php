<?php


namespace core\base\controller;


use core\base\exceptions\RouteException;


abstract class BaseController
{

    use BaseMethods;

    protected $controller;
    protected $parameters;
    protected $inputMethod;
    protected $outputMethod;

    protected $header;
    protected $content;
    protected $footer;
    protected $page;

    protected $styles;
    protected $scripts;

    protected $template;

    protected $errors;

    protected $ajaxData;

    public function route() {

        $controller = str_replace('/', '\\', $this->controller);

        try {
            $method = new \ReflectionMethod($controller, 'request');

            $parameters = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod,
            ];

            $method->invoke(new $controller, $parameters);

        }
        catch (\ReflectionException $e) {
            throw new RouteException($e->getMessage());
        }

    }

    public function request($args) {

        $this->parameters = $args['parameters'];
        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];

        $data = $this->$inputData();

        if (method_exists($this, $outputData)) {
            $page = $this->$outputData($data);
            if ($page) {
                $this->page = $page;
            }
        }
        else if ($data) {
            $this->page = $data;
        }

        if ($this->errors) {
            $this->writelog($this->errors);
        }

        $this->getPage();
    }

    protected function init() {

        if (defined('CSS_JS') && is_array(CSS_JS)) {
            foreach (CSS_JS as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $this->$key[] = PATH . trim($item, '/');
                    }
                }
                else {
                    $this->$key[] = PATH . trim($value, '/');
                }
            }
        }

    }

    protected function render($path = '', $parameters = []) {

        extract($parameters);

        if (!$path) {
            $path = TEMPLATE . explode('controller',
                strtolower((new \ReflectionClass($this))->getShortName()))[0];
        }

        ob_start();
        if (!@include_once $path . '.php') {
            throw new RouteException('Отсутствует шаблон - ' . $path);
        }
        return ob_get_clean();
    }

    protected function getPage() {

        if (is_array($this->page)) {
            foreach ($this->page as $block) {
                echo $block;
            }
        }
        else {
            echo $this->page;
        }
        exit;

    }

}