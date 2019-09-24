<?php

namespace component;

class Session extends \ObjectAccess
{
    public function set($name, $value = null, $expire = null)
    {
        session_start();
        if ($value == null) {
            if (isset($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
        } else {
            $_SESSION[$name] = $value;
        }
        if ($expire) {
            $_SESSION[$name . '_expire'] = $expire + time();
        }
        session_write_close();
    }

    public function get($name)
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
}