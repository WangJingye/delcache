<?php
function config($default = null)
{
    $config = require COMMON_PATH . 'config/config.php';
    if (!$config['default_module']) {
        $config['default_module'] = 'admin';
    }
    if (!$config['default_action']) {
        $config['default_action'] = 'index';
    }
    if (!$config['default_controller']) {
        $config['default_controller'] = 'index';
    }
    if ($default) {
        return isset($config[$default])?$config[$default]:'';
    }
    return $config;
}

function encryptPassword($password, $salt)
{
    return md5($salt . md5($password . $salt));
}
