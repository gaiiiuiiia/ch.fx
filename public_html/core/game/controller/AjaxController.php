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

                    $gameData = $this->gameManager->loadGame($this->matchID);

                    return $gameData;

                    break;

            }



        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}