<?php

namespace common\extend\encrypt;

class Encrypt
{

    public static function encryptPassword($password, $salt)
    {
        return md5($salt . md5($password . $salt));
    }

}