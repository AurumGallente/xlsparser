<?php

namespace App\Processors\Storages;

interface StorageInterface
{
    public static function get($var);
    public static function set($var, $val);
    public static function del($var);
}

