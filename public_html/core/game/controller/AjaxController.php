<?php


namespace core\game\controller;


class AjaxController extends BaseGame
{

    public function ajax() {

        if ($this->ajaxData['ajax']) {

            $this->execBase();

            foreach ($this->ajaxData as $key => $item) {
                $this->ajaxData[$key] = $this->clearStr($item);
            }

            switch ($this->ajaxData['ajax']) {

                case 'getPlayers':

                    $this->gameManager->loadGame(16);

                    break;

            }



        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}