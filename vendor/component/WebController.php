<?php
namespace component;

class WebController extends \Controller
{
    /**
     * 界面js
     * @var array
     */
    protected $scriptList = [
        '/static/js/jquery.js',
        '/static/plugin/bootstrap/js/popper.min.js',
        '/static/plugin/bootstrap/js/bootstrap.js',
        '/static/js/jquery.validate.js',
        '/static/js/select2.min.js',
        '/static/js/toastr.js',
        '/static/js/ztree.core.js',
        '/static/js/ztree.excheck.js',
        '/static/js/main.js',
    ];

    /**
     * 界面css
     * @var array
     */
    protected $cssList = [
        '/static/plugin/bootstrap/css/bootstrap.css',
        '/static/plugin/bootstrap/css/fonts.css',
        '/static/css/main.css',
        '/static/css/select2.css',
        '/static/css/ztree.css',
    ];

    /** @var \Request $request */
    public $request;

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->layout('main');
        if (!IS_INSTALL && \App::$request->module != 'install') {
            $this->redirect('install/index/agreement');
        }
        if (IS_INSTALL) {
            $user = \App::$session->get('user');
            \App::$user = $user ? $user : [];
            $this->validateUserGrant();
        }
        parent::init();
    }

    public function afterAction()
    {
        $this->display();
        parent::afterAction(); // TODO: Change the autogenerated stub
    }

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @param WebService $service
     * @return string
     */
    public function pagination($service)
    {
        $pageCount = 8;
        $leftCount = $rightCount = (int)(floor($pageCount / 2));
        //偶数时
        if ($pageCount % 2 == 0) {
            $leftCount = $rightCount - 1;
        }
        if ($service->totalPage <= $pageCount) {
            $startPage = 1;
            $endPage = $service->totalPage;
        } else if ($service->page + $rightCount >= $service->totalPage) {
            $startPage = $service->totalPage - $pageCount + 1;
            $endPage = $service->totalPage;
        } else if ($service->page - $leftCount <= 0) {
            $startPage = 1;
            $endPage = $pageCount;
        } else {
            $startPage = $service->page - $leftCount;
            $endPage = $service->page + $rightCount;
        }
        $params['pageSize'] = $service->pageSize;
        $html = '<div class="pagination-list"><div class="page-container">共' . $service->total . '条 <select name="pageSize" style="margin-left: 1rem;margin-right: .5rem;" id="page-size">';
        foreach ([10, 20, 50, 100] as $i) {
            $html .= '<option value="' . $i . '" ' . ($i == $service->pageSize ? 'selected' : '') . '>' . $i . '</option>';
        }
        $html .= '</select> 条/页</div>';
        if ($endPage > 1) {
            $params['page'] = $service->page == 1 ? 1 : $service->page - 1;
            $html .=
                '<ul class="pagination">';
            if ($service->page > 1) {
                $html .= '<li class="page-item">' .
                    '<a class="page-link" href="' . \App::$urlManager->createUrl(\App::$request->uri, $params) . '" aria-label="Previous">' .
                    '<span aria-hidden="true">&laquo;</span>' .
                    '<span class="sr-only">Previous</span>' .
                    '</a></li>';
                $params['page'] = 1;
                $html .= '<li class="page-item ' . ($service->page == 1 ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App::$urlManager->createUrl(\App::$request->uri, $params) . '">首页</a></li>';
            }
            for ($i = $startPage; $i <= $endPage; $i++) {
                $params['page'] = $i;
                $html .= '<li class="page-item ' . ($service->page == $i ? 'active' : '') . '"><a class="page-link" href="' . \App::$urlManager->createUrl(\App::$request->uri, $params) . '">' . $i . '</a></li>';
            }
            if ($service->page < $service->totalPage) {
                $params['page'] = $service->totalPage;
                $html .= '<li class="page-item ' . ($service->page == $service->totalPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App::$urlManager->createUrl(\App::$request->uri, $params) . '">末页</a></li>';
                $params['page'] = $service->page == $endPage ? $endPage : $service->page + 1;
                $html .= '<li class="page-item ' . ($service->page == $endPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App::$urlManager->createUrl(\App::$request->uri, $params) . '" aria-label="Next">' .
                    '<span aria-hidden="true">&raquo;</span>' .
                    '<span class="sr-only">Next</span>' .
                    '</a></li></ul></div>';
            }

        }
        return $html;
    }

    /**
     * 文件上传处理
     * @param $file
     * @param bool $is_image
     * @return string
     * @throws \Exception
     */
    public function parseFile($file, $is_image = true)
    {
        $ext_arr = [];
        if ($is_image) {
            $ext_arr = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        }
        $arr = explode('.', $file['name']);
        $ext = end($arr);
        if (!in_array($ext, $ext_arr)) {
            throw new \Exception('不允许的文件类型,只支持' . implode('/', $ext_arr));
        }
        $filename = '/upload/system/image/' . md5_file($file['tmp_name']) . '.' . $ext;
        if (!file_exists(PUBLIC_PATH . $filename)) {
            if (@!move_uploaded_file($file['tmp_name'], PUBLIC_PATH . $filename)) {
                throw new \Exception('文件保存失败');
            }
        }
        return $filename;
    }

    /**
     * 验证用户权限
     * @throws \Exception
     */
    private function validateUserGrant()
    {
        try {
            $uri = \App::$request->uri;
            if ($this->checkNoLoginList(\App::$request->module . '/' . \App::$request->controller, \App::$request->action)) {
                return;
            }
            if (empty(\App::$user)) {
                throw new \Exception('您暂未登陆', 1111);
            }
            if ($this->checkWhiteList(\App::$request->module . '/' . \App::$request->controller, \App::$request->action)) {
                return;
            }
            $menu = \Db::table('Menu')->where(['url' => $uri])->find();
            if (!$menu) {
                throw new \Exception($uri . '该地址不在权限中');
            }
            if (\App::$user['identity'] == 1) {
                return;
            }
            $access = \Db::table('RoleMenu')->rename('a')
                ->join(['b' => 'RoleAdmin'], 'a.role_id = b.role_id')
                ->where(['a.menu_id' => $menu['id'], 'b.admin_id' => \App::$user['admin_id']])
                ->find();
            if (!$access) {
                throw new \Exception('您暂无该权限');
            }
        } catch (\Exception $e) {
            if (!\App::$request->isAjax() && $e->getCode() == 1111) {
                $this->redirect('system/public/login');
            }
            $this->error($e->getMessage());
        }
    }

    /**
     * @param $uri
     * @throws \Exception
     */
    /**
     * @param $moduleName
     * @param null $actionName
     * @return bool
     * @throws \Exception
     */
    public function checkNoLoginList($moduleName, $actionName = null)
    {
        $noLoginActions = \App::$config->actionNoLoginList;

        $moduleName = strtolower($moduleName);
        $actionName = strtolower($actionName);
        $_deal_action = [];
        foreach ($noLoginActions as $m => $a) {
            array_walk($a, function (&$x) {
                $x = strtolower($x);
            });
            $_deal_action[strtolower($m)] = $a;
        }
        if (isset($_deal_action[$moduleName]) && in_array($actionName, $_deal_action[$moduleName])) {
            return true;
        }
        return false;
    }

    /* @param $moduleName
     * @param null $actionName
     * @return bool
     * @throws \Exception
     */
    public function checkWhiteList($moduleName, $actionName = null)
    {
        $noLoginActions = \App::$config->actionWhiteList;

        $moduleName = strtolower($moduleName);
        $actionName = strtolower($actionName);
        $_deal_action = [];
        foreach ($noLoginActions as $m => $a) {
            array_walk($a, function (&$x) {
                $x = strtolower($x);
            });
            $_deal_action[strtolower($m)] = $a;
        }
        if (isset($_deal_action[$moduleName]) && in_array($actionName, $_deal_action[$moduleName])) {
            return true;
        }
        return false;
    }

    /**
     * 自动渲染使用
     * @param $module
     * @param $controller
     * @param $action
     */
    public function display($uri = '')
    {
        $route = $uri != '' ? explode('/', trim($uri)) : [];
        $view = '';
        if (count($route) == 0) {
            $action = \App::$request->action . '.php';

            $view = APP_PATH . \App::$request->module . '/views/' . \App::$request->controller . '/';
        } else if (count($route) == 1) {
            $action = $uri . '.php';

            $view = APP_PATH . \App::$request->module . '/views/' . \App::$request->controller . '/';
        } else if (count($route) == 2) {
            $action = $route[1] . '.php';

            $view = APP_PATH . \App::$request->module . '/views/' . $route[0] . '/';
        } else if (count($route) == 3) {
            $action = $route[2] . '.php';
            $view = APP_PATH . $route[0] . '/views/' . $route[1] . '/';
        }
        $view = $view . strtolower(trim(preg_replace('/([A-Z])/', '-$1', $action)));
        if (!$view || !file_exists($view)) {
            throw new \Exception('view is missing!', 500);
        }
        include APP_PATH . 'common/layout/' . $this->layout . '.php';
        exit();
    }

    /**
     * @param string $message
     * @param array $data
     * @throws \Exception
     */
    public function success($message = '', $data = [])
    {
        if (\App::$request->isAjax()) {
            parent::success($message, $data);
        }
    }

    /**
     * @param string $message
     * @param array $data
     */
    public function error($message = '', $data = [])
    {
        if (\App::$request->isAjax()) {
            parent::error($message, $data);
        }
    }


    public function redirect($url, $option = [])
    {
        $url = strpos($url, 'http') !== false ? $url : \App::$urlManager->createUrl($url, $option);
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
            $script = '/static/js/' . APP . '/' . \App::$request->module . '/' . $script;
        }
        if (!in_array($script, $this->scriptList)) {
            $this->scriptList[] = $script;
        }
        return $this;
    }

    public function appendCss($css)
    {
        if (strpos($css, '/') !== 0) {
            $css = '/static/' . $css;
        }
        if (!in_array($css, $this->cssList)) {
            $this->cssList[] = $css;
        }
        return $this;
    }

}