<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/1
 * Time: 10:02 AM
 */

namespace common\extend\redis;
class RedisConnect
{
    /** @var \Redis */
    static $redis;

    public static function getInstance()
    {
        if (static::$redis == null) {
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $redis->auth('90lucker');
            static::$redis = $redis;
        }
        return static::$redis;
    }

    public function __construct($host, $auth)
    {

    }
}