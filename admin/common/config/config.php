<?php
if (file_exists(COMMON_PATH . 'config/config.php')) {
    $runtimeConfig = include COMMON_PATH . "config/config.php";
} else {
    $runtimeConfig = [];
}
$configs = [
    'actionNoLoginList' => [
        'system/public' => ['login', 'logout', 'captcha'],
    ],
    'actionWhiteList' => [
        'system/admin' => ['profile','changePassword','changeProfile']
    ]
];
return array_merge($runtimeConfig, $configs);