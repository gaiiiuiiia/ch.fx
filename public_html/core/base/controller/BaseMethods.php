<?php


namespace core\base\controller;


trait BaseMethods {

    protected function writeLog($message, $file = 'log.txt', $event = 'Fault'){

        $dateTime = new \DateTime();

        $content = $event . ': ' . $dateTime->format('d-m-Y G:i:s') . ' - ' . $message . "\r\n";

        file_put_contents('log/' . $file, $content, FILE_APPEND);

    }

    protected function redirect($http = false, $code = false){

        if ($code) {
            $codes = [
                '301' => 'HTTP/1.1 301 Move Permanently',
            ];

            if ($codes[$code]) {
                header($codes[$code]);
            }
        }

        if ($http) {
            $redirect = $http;
        }
        else {
            $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
        }

        exit(header("Location: $redirect"));
    }

    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isAjax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    protected function getStyles(){
        if ($this->styles){
            foreach ($this->styles as $style){
                echo "<link rel='stylesheet' href='$style'>";
            }
        }
    }

    protected function getScripts(){
        if ($this->scripts){
            foreach ($this->scripts as $script){
                echo "<script src='$script'></script>";
            }
        }
    }

}