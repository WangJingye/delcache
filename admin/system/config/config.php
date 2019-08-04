<?php
if (file_exists(APP_PATH . 'common/config/config.php')) {
    $runtimeConfig = include APP_PATH . 'common/config/config.php';
} else {
    $runtimeConfig = [];
}
$configs = [
    'default_password' => '123456'
];
return array_merge($runtimeConfig, $configs);