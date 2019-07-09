<?php

namespace core;

class Config
{
    private static $config = [];


    public static function init()
    {
        $file = COMMON_PATH . 'config/config.php';
        self::load($file);
    }

    /**
     * @param null $name
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function get($name = null)
    {
        $module = Request::instance()->module;
        if ($module) {
            $file = APP_PATH . $module . '/config/config.php';
            if (!file_exists($file)) {
                $file = APP_PATH . '/common/config/config.php';
            }
            is_file($file) && self::load($file);
        }
        if (empty($name)) {
            return self::$config;
        }
        return isset(self::$config[$name]) ? self::$config[$name] : '';
    }

    public static function load($file)
    {

        if (is_file($file)) {
            $config = include $file;
            if (!$config['default_module']) {
                $config['default_module'] = 'admin';
            }
            if (!$config['default_action']) {
                $config['default_action'] = 'index';
            }
            if (!$config['default_controller']) {
                $config['default_controller'] = 'index';
            }
            self::$config = array_merge(self::$config, $config);
        }
        return self::$config;
    }

}