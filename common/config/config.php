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
        'default_module' => 'common',
        // 禁止访问模块
        'deny_module_list' => ['common'],
        // 默认控制器名
        'default_controller' => 'home',
        // 默认操作名
        'default_action' => 'index',
    ],
    'console' => [
        // 默认模块名
        'default_module' => 'common',
        // 禁止访问模块
        'deny_module_list' => ['common'],
        // 默认控制器名
        'default_controller' => 'home',
        // 默认操作名
        'default_action' => 'index',
    ],
    'wechat' => [
        'app_id' => '',//小程序ID
        'app_secret' => '',//小程序密钥
        'mch_id' => '',//商户ID
        'pay_key' => '',//商户支付Key
    ],
    'web_info' => [
        'host' => 'https://www.delcache.com',
        'ip' => '121.40.224.59',
    ],
];
return $configs;