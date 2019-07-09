<?php
if (file_exists(COMMON_PATH . 'config/config.php')) {
    $runtimeConfig = include COMMON_PATH . "config/config.php";
} else {
    $runtimeConfig = [];
}
$configs = [
    'actionWhiteList' => [
        'system/public' => ['login', 'logout', 'captcha', 'error'],
    ]
];
return array_merge($runtimeConfig, $configs);