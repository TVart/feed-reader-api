<?php

namespace App\Services;

class ApiFactory
{
    public static function createFromSource(string $source) : Api
    {
        $class_name = "App\Services\\".ucfirst(strtolower($source)) . 'Api';
        return new $class_name();
    }
}
