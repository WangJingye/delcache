<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 8:55 PM
 */

namespace core;

class Request
{
    public $action;
    public $controller;
    public $module;
    public $config;
    public $params;

    public $uri;
    public $defaultUri;

    public static $instance;

    /**
     * Db constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct()
    {

    }

    /**
     * @throws \Exception
     */
    public function parseParams()
    {
        $res = $this->parseUri(isset($_GET['s']) ? $_GET['s'] : '');
        $this->module = $res['module'];
        $this->action = $res['action'];
        $this->controller = $res['controller'];
        $this->uri = $this->module . '/' . $this->controller . '/' . $this->action;
        foreach ($res['params'] as $key => $v) {
            $_GET[$key] = $v;
            $_REQUEST[$key] = $v;
        }
        unset($_GET['s']);
        unset($_REQUEST['s']);
        if (in_array($this->module, $this->config['deny_module_list'])) {
            throw new \Exception('module is deny', 404);
        }
        $this->params = $this->trimString($_REQUEST);
    }

    /**
     * @return Request
     * @throws \Exception
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param $uri
     * @return array
     * @throws \Exception
     */
    public function parseUri($uri)
    {
        $route = $uri ? explode('/', $uri) : [];
        $config = $this->config;
        if (!$config) {
            $config = Config::get();
            $this->defaultUri = $config['default_module'] . '/' . $config['default_controller'] . '/' . $config['default_action'];
            $this->config = $config;
        }
        $params = [];
        if (count($route) == 0) {
            $action = $config['default_action'];
            $controller = $config['default_controller'];
            $module = $config['default_module'];
        } else if (count($route) == 1) {
            $action = $config['default_action'];
            $controller = $route[0];
            $module = $config['default_module'];
        } else if (count($route) == 2) {
            $action = $route[1];
            $controller = $route[0];
            $module = $config['default_module'];
        } else {
            $action = $route[2];
            $controller = $route[1];
            $module = $route[0];
            for ($i = 3; $i < count($route); $i += 2) {
                $params[$route[$i]] = isset($route[$i + 1]) ? $route[$i + 1] : '';
            }
        }
        return [
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'params' => $params,
        ];
    }

    public function method()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function contentType()
    {
        $contentType = $_SERVER['CONTENT_TYPE'];
        if ($contentType) {
            if (strpos($contentType, ';')) {
                list($type) = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }
        return '';
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为Ajax请求
     * @access public
     * @return bool
     */
    public function isAjax()
    {
        return ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']) || !empty($_GET['ajax'])) ? true : false;
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost()
    {
        return $this->method() == 'POST';
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut()
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead()
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch()
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions()
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * @param $name
     * @param null $default
     * @return null|string
     */
    public function getParams($name, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        if ($default !== null) {
            return $default;
        }
        return '';
    }

    protected function trimString($params)
    {
        foreach ($params as $k => $param) {
            if (is_array($param)) {
                $params[$k] = $this->trimString($param);
            } else {
                $_str = trim($param);
                //过滤用户输入的bom
                $params[$k] = str_replace(chr(239) . chr(187) . chr(191), '', $_str);
            }
        }
        return $params;
    }
}