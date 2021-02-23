<?php


namespace core\game\classes;


class DBDumper extends Dumper
{
    public function saveDataToDB(int $matchID = null): int
    {
        $matchID = $matchID ?:
            $this->model->add('matches', [
                'fields' => [
                    'date' => 'NOW()',
                    'players' => $this->data['playerNames'],
                ],
                'return_id' => true,
            ]);

        $this->model->add('match_logs', [
            'fields' => [
                'match_id' => $matchID,
                'state' => json_encode($this->data),
            ],
        ]);

        return $matchID;
    }
}