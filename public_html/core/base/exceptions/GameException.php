<?php


namespace core\base\exceptions;


use core\base\controller\BaseMethods;


class GameException extends \Exception
{

    use BaseMethods;

    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);

        $error = $this->getMessage() ?: 'Ошибка с кодом - ' . $this->getCode();

        $error .= "\r\n" . 'file ' . $this->getFile() . "\r\n" . 'On line ' . $this->getLine() . "\r\n";

        $this->writeLog($error, 'game_log.txt');

    }

}