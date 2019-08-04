<?php
$configs = [
    'admin' => [
        // 默认模块名
        'default_module' => 'system',
        // 禁止访问模块
        'deny_module_list' => ['common'],
        // 默认控制器名
        'default_controller' => 'menu',
        // 默认操作名
        'default_action' => 'index',
    ],
    'api' => [
        // 默认模块名
        'default_module' => 'home',
        // 禁止访问模块
        'deny_module_list' => ['common'],
        // 默认控制器名
        'default_controller' => 'index',
        // 默认操作名
        'default_action' => 'index',
    ],
];
return $configs;