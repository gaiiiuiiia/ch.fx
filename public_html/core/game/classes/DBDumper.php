<?php


namespace core\game\classes;


class DBDumper extends Dumper
{

    public function dump()
    {
        foreach ($this->data as $prop => $value) {



        }


        $this->write();
    }

    protected function write()
    {

    }

}