<?php


namespace core\base\exceptions;


use core\base\controller\BaseMethods;

class RouteException extends \Exception
{
    protected $messages;

    use BaseMethods;

    public function __construct($message = "", $code = 0){

        parent::__construct($message, $code);

        $this->messages = include 'messages.php';

        $error = $this->getMessage() ?: $this->messages[$this->getCode()];

        $error .= "\r\n" . 'file ' . $this->getFile() . "\r\n" . 'On line ' . $this->getLine() . "\r\n";

        $this->writeLog($error);

    }
}