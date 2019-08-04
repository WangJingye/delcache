<?php

namespace common\extend\redis;
class RedisConnect
{
    /** @var \Redis */
    static $redis;

    public static function instance()
    {
        if (static::$redis == null) {
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $redis->auth('90lucker');
            static::$redis = $redis;
        }
        return static::$redis;
    }
}