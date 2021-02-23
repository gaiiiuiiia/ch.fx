<?php


namespace core\game\classes;


class Loader extends DataManager
{

    public function loadData(int $id) : array
    {
        // последняя запись в таблице match_logs с match_id = $matchID
        $gameData = $this->model->get('match_logs', [
            'fields' => [],
            'where' => ['match_id' => $id],
            'order' => ['id'],
            'order_direction' => ['DESC'],
            'limit' => 1,
        ])[0]['state'];

        return json_decode($gameData, true);
    }

}