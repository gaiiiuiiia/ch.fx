<?php


namespace core\game\interfaces;


interface IDumper
{

    public function setData(IDumpable $obj);

    public function dump();

}