<?php

//自动加载类
spl_autoload_register(function ($classname) {
    if (file_exists(BASE_PATH . 'vendor/' . str_replace('\\', '/', $classname) . '.php')) {
        require_once BASE_PATH . 'vendor/' . str_replace('\\', '/', $classname) . '.php';
    } else if (file_exists(BASE_PATH . str_replace('\\', '/', $classname) . '.php')) {
        require_once BASE_PATH . str_replace('\\', '/', $classname) . '.php';
    }
});