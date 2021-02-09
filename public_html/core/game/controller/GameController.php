<?php


namespace core\game\controller;


class GameController extends BaseGame
{

    protected function inputData()
    {

        $this->execBase();

        $this->template = TEMPLATE . 'game';

        return [
            'sizex' => 5,
            'sizey' => 7,
        ];

    }

}