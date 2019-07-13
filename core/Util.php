<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/10
 * Time: 3:11 PM
 */

namespace core;

class Util
{
    public static function session_set($name, $value = null, $expire = null)
    {
        session_start();
        $_SESSION[$name] = $value;
        if ($expire) {
            $_SESSION[$name . '_expire'] = $expire + time();
        }
        session_write_close();
    }

    public static function session_get($name)
    {
        $value = null;
        session_start();
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
        }
        if (isset($_SESSION[$name . '_expire']) && $_SESSION[$name . '_expire'] < time()) {
            $value = null;
        }
        session_write_close();
        return $value;
    }

    public static function session_unset($name)
    {
        session_start();
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
        session_write_close();
    }
}