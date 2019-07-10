<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:20 PM
 */

namespace core;

class Core
{

    /**
     * @throws \Exception
     */
    public static function run()
    {
        try {
            //初始化配置文件
            Config::init();
            //处理请求
            $request = Request::instance();
            $request->parseParams();
            $action = $request->action;
            $controller = $request->controller;
            $controller = ucfirst($controller) . 'Controller';
            $controller = ('admin\\' . $request->module . '\\controller' . '\\' . $controller);
            if (!file_exists(str_replace('\\', '/', BASE_PATH . $controller . '.php'))) {
                throw new \Exception('Controller is not exist', 404);
            }

            /** @var Controller $controller */
            $controller = new $controller();
            if (!in_array($action, get_class_methods($controller))) {
                throw new \Exception('Action is not exist', 404);
            }
            $controller->request = $request;
            $controller->layout('main');
            $controller->init();
            //执行action
            $controller->$action();
            //渲染界面
            $controller->display();
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
                $msg = $e->getMessage();
            }
            echo $msg;
        }
    }
}