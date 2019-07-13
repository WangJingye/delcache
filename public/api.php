<?php


// 调试模式开关
define("APP_DEBUG", true);

define('BASE_PATH', dirname(__DIR__) . '/');
define('PUBLIC_PATH', dirname(__FILE__) . '/');

define('APP', 'api');
define('APP_PATH', BASE_PATH . 'admin/');
define('CORE_PATH', BASE_PATH . 'core/');
define('COMMON_PATH', BASE_PATH . 'common/');
ini_set('date.timezone', 'Asia/Shanghai');
//自动加载类
spl_autoload_register(function($classname) {
    require_once BASE_PATH . str_replace('\\', '/', $classname) . '.php';
});
if (APP_DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
}
\core\Core::run();