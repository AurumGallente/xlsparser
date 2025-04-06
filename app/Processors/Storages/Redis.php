<?php

namespace App\Processors\Storages;

use App\Processors\Storages\StorageInterface;
use Illuminate\Support\Facades\Redis as RedisFacade;


class Redis extends RedisFacade implements StorageInterface
{
    public static function set($var, $val) {
        RedisFacade::set($var, $val);
    }

    public static function get($var)
    {
        return RedisFacade::get($var);
    }

    public static function del($var)
    {
        RedisFacade::del($var);
    }
}
