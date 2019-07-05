<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:25 PM
 */

namespace core;

class Controller
{
    /** @var Controller $this */

    protected $var = [];

    protected $scriptList = [];

    /** @var Request $request */
    public $request;

    public function init()
    {
    }

    public function __construct()
    {
    }

    public function assign($key, $value)
    {
        $this->$key = $value;
    }

    public function __set($name, $value)
    {
        if (trim($name) != '') {
            $this->var[$name] = $value;
        }
    }

    public function __get($name)
    {
        return isset($this->var[$name]) ? $this->var[$name] : '';
    }

    /**
     * 自动渲染使用
     * @param $module
     * @param $controller
     * @param $action
     * @throws \Exception
     */
    public function display($uri = '')
    {
        $route = $uri != '' ? explode('/', trim($uri)) : [];
        $view = '';
        if (count($route) == 0) {
            $action = $this->request->action . '.php';

            $view = APP_PATH . $this->request->module . '/views/' . $this->request->controller . '/';
        } else if (count($route) == 1) {
            $action = $uri . '.php';

            $view = APP_PATH . $this->request->module . '/views/' . $this->request->controller . '/';
        } else if (count($route) == 2) {
            $action = $route[1] . '.php';

            $view = APP_PATH . $this->request->module . '/views/' . $route[0] . '/';
        } else if (count($route) == 3) {
            $action = $route[2] . '.php';
            $view = APP_PATH . $route[0] . '/views/' . $route[1] . '/';
        }
        $view = $view . strtolower(trim(preg_replace('/([A-Z])/', '-$1', $action)));
        if (!$view || !file_exists($view)) {
            throw new \Exception('view is missing!', 500);
        }
        include COMMON_PATH . 'layout/' . $this->layout . '.php';
        exit();
    }


    public function success($message = '', $data = [])
    {
        $result = [
            'errno' => 0,
            'message' => $message,
            'data' => $data,
        ];
        echo json_encode($result);
        exit;
    }

    /**
     * @param string $message
     * @param array $data
     * @throws \Exception
     */
    public function error($message = '', $data = [])
    {
        if ($this->request->isAjax()) {
            $result = [
                'errno' => 1,
                'message' => $message,
                'data' => $data,
            ];
            echo json_encode($result);
            exit;
        }
        $this->layout('empty');
        $this->assign('message', $message);
        $this->assign('data', $data);
        $this->display('public/error');
    }

    public function createUrl($uri, $option = [])
    {
        $res = $this->request->parseUri($uri);
        $option = array_merge($res['params'], $option);
        $url = '/' . $res['module'] . '/' . $res['controller'] . '/' . $res['action'];
        if (count($option)) {
            $url .= '?' . http_build_query($option);
        }
        return $url;
    }

    public function redirect($url,$option)
    {
        $url = strpos($url, 'http') !== false ? $url : $this->createUrl($url,$option);
        //多行URL地址支持
        if (!headers_sent()) {
            // redirect
            header('Location: ' . $url);
            exit;
        } else {
            $str = "<meta http-equiv='Refresh' content='URL={$url}'>";
            exit($str);
        }
    }

    public function layout($default = 'main')
    {
        $this->layout = $default;
    }

    public function appendScript($script)
    {
        if (strpos($script, '/') !== 0) {
            $script = '/static/js/' . $script;
        }
        if (!in_array($script, $this->scriptList)) {
            $this->scriptList[] = $script;
        }
        return $this;
    }
}