<?php

use component\Config;
use component\Session;
use component\UrlManager;

class App
{
    /** @var \Request $request */
    public static $request;

    /** @var array $user */
    public static $user;

    /** @var UrlManager $urlManager */
    public static $urlManager;

    /** @var Config */
    public static $config;

    /** @var Session $session */
    public static $session;

    public static function init()
    {
        static::$config = new Config();
        static::$urlManager = new UrlManager();
        static::$session = new Session();
    }

    public static function run()
    {
        try {
            //初始化配置文件
            self::init();
            //处理请求
            $request = Request::instance();
            $request->parseParams();
            self::$request = $request;
            $action = $request->action;
            $arr = explode('-', $action);
            $i = 0;
            foreach ($arr as $key => $v) {
                if ($i != 0) {
                    $arr[$key] = ucfirst($v);
                }
                $i++;
            }
            $action = implode('', $arr) . 'Action';
            $controller = $request->controller;
            $arr = explode('-', $controller);
            foreach ($arr as $key => $v) {
                $arr[$key] = ucfirst($v);
            }
            $controller = implode('', $arr) . 'Controller';
            $controller = (APP . '\\' . $request->module . '\\controller' . '\\' . $controller);
            if (!file_exists(str_replace('\\', '/', BASE_PATH . $controller . '.php'))) {
                throw new \Exception('Controller is not exist', 404);
            }
            /** @var Controller $controller */
            $controller = new $controller();
            if (!in_array($action, get_class_methods($controller))) {
                throw new \Exception('Action is not exist', 404);
            }
            //执行action
            $controller->beforeAction();
            $controller->$action();
            $controller->afterAction();

        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            if ($errorCode == 0) {
                $errorCode = 500;
            }
            header('status:' . $errorCode);
            $errorMsg = [
                400 => '400 BAD REQUEST',
                404 => '404 NOT FOUND',
                500 => '500 Internal Server Error',
            ];
            if (!APP_DEBUG) {
                $view = COMMON_PATH . 'layout/' . $errorCode . '.php';
                if (file_exists($view)) {
                    include $view;
                    exit();
                }
                $msg = $errorMsg[$errorCode];
            } else {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
            echo $msg;
        }
    }

}