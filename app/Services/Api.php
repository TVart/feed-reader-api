<?php

namespace App\Services;

interface Api
{
    /**
     * @return array
     */
    public function getAll() : array;

    /**
     * @param $id
     * @return array
     */
    public function getOne($id) : array;
}
