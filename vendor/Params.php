<?php

class Params extends \ObjectAccess
{

    public function load($data)
    {
        foreach ($data as $key => $v) {
            $this->$key = $v;
        }
    }
}