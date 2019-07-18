<?php

define('APP', 'admin');
define("APP_VERSION", 'V1.0.0');

// 调试模式开关
define("APP_DEBUG", true);

define('BASE_PATH', dirname(__DIR__) . '/');
define('PUBLIC_PATH', dirname(__FILE__) . '/');

define('APP_PATH', BASE_PATH . 'admin/');
define('CORE_PATH', BASE_PATH . 'core/');
define('COMMON_PATH', BASE_PATH . 'common/');
ini_set('date.timezone', 'Asia/Shanghai');
//自动加载类
spl_autoload_register(function ($classname) {
    require_once BASE_PATH . str_replace('\\', '/', $classname) . '.php';
});
if (APP_DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
}
define('IS_INSTALL', file_exists(COMMON_PATH . 'config/install.lock'));
\core\Core::run();