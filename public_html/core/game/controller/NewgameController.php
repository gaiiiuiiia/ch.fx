<?php


namespace core\game\controller;


class NewgameController extends BaseGame
{

    protected function inputData()
    {
        $this->execBase();

        $this->template = TEMPLATE . 'newgame';

        unset($_SESSION['match_id']);
    }


}