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

                case 'getGameData':

                    $gameData = $this->gameManager->loadGame($this->matchID);

                    return $gameData;

                    break;

                case 'endGame':

                    unset($_SESSION['match_id']);

                    return PATH;

                    break;

            }



        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}