<?php

namespace component;

class Config extends \ObjectAccess
{
    public $instance = [];


    public function __construct()
    {
        $file = COMMON_PATH . 'config/config.php';
        $this->load($file);
        $this->instance = $this->instance[APP];
    }

    /**
     * @param null $name
     * @return array|mixed|string
     * @throws \Exception
     */
    public function __get($name = null)
    {
        $module = \Request::instance()->module;
        if ($module) {
            $file = APP_PATH . $module . '/config/config.php';
            if (!file_exists($file)) {
                $file = APP_PATH . '/common/config/config.php';
            }
            is_file($file) && self::load($file);
        }
        if (empty($name)) {
            return $this->instance;
        }
        return isset($this->instance[$name]) ? $this->instance[$name] : '';
    }

    public function load($file)
    {
        if (is_file($file)) {
            $instance = include $file;
            $this->instance = array_merge($this->instance, $instance);
        }
        return $this->instance;
    }
}