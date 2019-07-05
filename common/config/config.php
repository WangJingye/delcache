<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:01 PM
 */

$configs = [
    // 默认模块名
    'default_module' => 'system',
    // 禁止访问模块
    'deny_module_list' => ['common'],

    'module_list' => ['system'],
    // 默认控制器名
    'default_controller' => 'menu',
    // 默认操作名
    'default_action' => 'index',
];
return $configs;