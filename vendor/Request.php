<?php

class Request
{
    public $action;
    public $controller;
    public $module;
    public $config;
    public $params;

    public $controllerNamespace;//控制器命名空间
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
        //获取controller命名空间
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
            $config = \App::$config->instance;
            $this->defaultUri = $config['default_module'] . '/' . $config['default_controller'] . '/' . $config['default_action'];
            $this->config = $config;
        }
        $params = [];
        $action = $config['default_action'];
        $controller = $config['default_controller'];
        $module = $config['default_module'];
        if (count($route) == 1) {
            throw new Exception('page not found');
        }
        if (count($route) >= 2) {
            $module = $route[0];
            $controller = $route[1];
        }
        $this->controllerNamespace = $this->getControllerNamespace($controller, $module);
        if (get_parent_class($this->controllerNamespace) == 'component\RestController') {
            switch ($this->method()) {
                case 'GET':
                    $action = 'index';
                    break;
                case 'POST':
                    $action = 'create';
                    break;
                case 'PUT':
                    $action = 'update';
                    break;
                case 'DELETE':
                    $action = 'delete';
                    break;
            }
            if (count($route) >= 3) {
                $params['id'] = $route[2];
            }
        } else if (count($route) == 2) {
            throw new Exception('page not found');
        } else if (count($route) >= 3) {
            $action = $route[2];
        }
        for ($i = 3; $i < count($route); $i += 2) {
            $params[$route[$i]] = isset($route[$i + 1]) ? $route[$i + 1] : '';
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

    protected function getControllerNamespace($controller, $module)
    {
        $arr = explode('-', $controller);
        foreach ($arr as $key => $v) {
            $arr[$key] = ucfirst($v);
        }
        $controller = implode('', $arr) . 'Controller';
        return (APP . '\\' . $module . '\\controller' . '\\' . $controller);
    }
}