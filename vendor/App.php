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
            if ($request->controller == 'generate' && $request->module == self::$config->default_module) {
                self::generateHtml();
                exit;
            }
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
            $controller = $request->controllerNamespace;
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

    public static function generateHtml()
    {
        $data = [];
        if (self::$request->isPost()) {
            $data = self::$request->params;
            if ($data['type'] == 'show-table') {
                try {
                    $fields = Db::table($data['table'])->getFields();
                    echo json_encode(['code' => '0', 'data' => array_keys($fields)]);

                } catch (Exception $e) {
                    echo json_encode(['code' => '1', 'message' => $e->getMessage()]);
                }
                die;
            } else {
                if (isset($data['fcomment'])) {
                    generate\Generate::instance($data)->run();
                }
            }
        }
        $view = BASE_PATH . 'vendor/generate/view.php';
        include $view;
    }

}