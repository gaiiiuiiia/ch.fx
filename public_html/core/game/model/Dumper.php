<?php


namespace core\game\model;


use core\base\controller\Singleton;

class Dumper extends Model
{

    use Singleton;

    public function saveDataToDB($data, $matchID = false) {

        if ($data) {

            $matchID = $matchID ?:
                $this->add('matches', [
                    'fields' => [
                        'date' => 'NOW()',
                        'players' => $data['playerNames'],
                    ],
                    'return_id' => true,
                ]);

            $dumpData = $data['map']->_getDump();
            $dumpData['turnToMove'] = $data['turnToMove'];

            $this->add('match_logs', [
                'fields' => [
                    'match_id' => $matchID,
                    'state' => json_encode($dumpData),
                ],
            ]);

            return $matchID;

        }

    }

    public function loadDataFromDB($matchID) {

        // последняя запись в таблице match_logs с match_id = $matchID
        $gameData = $this->get('match_logs', [
            'fields' => ['state'],
            'where' => ['match_id' => $matchID],
            'order_direction' => 'DESC',
            'limit' => 1,
        ])[0]['state'];

        return json_decode($gameData);

    }

}