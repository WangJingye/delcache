<?php

namespace admin\common\controller;

use admin\common\service\BaseService;
use admin\system\service\MenuService;
use core\Config;
use core\Controller;
use core\Db;
use core\Request;
use core\Util;

class BaseController extends Controller
{
    public $user;

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

    /** @var Request $request */
    public $request;

    /**
     * @throws \Exception
     */
    public function init()
    {
        $user = Util::session_get('user');
        $this->user = $user ? $user : [];
        $this->validateUserGrant();
        $this->setMenu();
        parent::init();
    }

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @param BaseService $service
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
        $html = '';
        if ($endPage > 1) {
            $params['page'] = $service->page == 1 ? 1 : $service->page - 1;
            $html .= '<div class="pagination-list"><div class="page-container">共' . $service->total . '条 <select name="pageSize" style="margin-left: 1rem;margin-right: .5rem;" id="page-size">';
            foreach ([10, 20, 50, 100] as $i) {
                $html .= '<option value="' . $i . '" ' . ($i == $service->pageSize ? 'selected' : '') . '>' . $i . '</option>';
            }
            $html .= '</select> 条/页</div>' .
                '<ul class="pagination">';
            if ($service->page > 1) {
                $html .= '<li class="page-item">' .
                    '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '" aria-label="Previous">' .
                    '<span aria-hidden="true">&laquo;</span>' .
                    '<span class="sr-only">Previous</span>' .
                    '</a></li>';
                $params['page'] = 1;
                $html .= '<li class="page-item ' . ($service->page == 1 ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '">首页</a></li>';
            }
            for ($i = $startPage; $i <= $endPage; $i++) {
                $params['page'] = $i;
                $html .= '<li class="page-item ' . ($service->page == $i ? 'active' : '') . '"><a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '">' . $i . '</a></li>';
            }
            if ($service->page < $service->totalPage) {
                $params['page'] = $service->totalPage;
                $html .= '<li class="page-item ' . ($service->page == $service->totalPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '">末页</a></li>';
                $params['page'] = $service->page == $endPage ? $endPage : $service->page + 1;
                $html .= '<li class="page-item ' . ($service->page == $endPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '" aria-label="Next">' .
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
            $uri = $this->request->uri;
            $menu = Db::table('Menu')->where(['url' => $uri])->find();
            if ($this->checkWhiteList($this->request->module . '/' . $this->request->controller, $this->request->action)) {
                return;
            }
            if (empty($this->user)) {
                throw new \Exception('您暂未登陆', 1111);
            }
            if (!$menu) {
                throw new \Exception($uri . '该地址不在权限中');
            }
            if ($this->user['identity'] == 1) {
                return;
            }
            $access = Db::table('RoleMenu')->rename('a')
                ->join(['b' => 'RoleAdmin'], 'a.role_id = b.role_id')
                ->where(['a.menu_id' => $menu['id'], 'b.admin_id' => $this->user['admin_id']])
                ->find();
            if (!$access) {
                throw new \Exception('您暂无该权限');
            }
        } catch (\Exception $e) {
            if (!$this->request->isAjax() && $e->getCode() == 1111) {
                $this->redirect('system/public/login');
            }
            $this->error($e->getMessage());
        }
    }

    /**
     * @param $uri
     * @throws \Exception
     */
    private function setMenu()
    {
        if (!empty($this->user)) {
            $admin_id = $this->user['admin_id'];
            $menus = (new menuService())->menus($admin_id, $this->request->uri);
            $this->assign('menus', $menus);
        }

    }

    /**
     * @param $moduleName
     * @param null $actionName
     * @return bool
     * @throws \Exception
     */
    public function checkWhiteList($moduleName, $actionName = null)
    {
        $action = Config::get('actionWhiteList');

        $moduleName = strtolower($moduleName);
        $actionName = strtolower($actionName);
        $_deal_action = [];
        foreach ($action as $m => $a) {
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

    /**
     * @param string $message
     * @param array $data
     * @throws \Exception
     */
    public function success($message = '', $data = [])
    {
        if ($this->request->isAjax()) {
            parent::success($message, $data);
        }
        $this->layout('empty');
        $this->assign('message', $message);
        $this->assign('data', $data);
        $this->display('public/success');
    }

    /**
     * @param string $message
     * @param array $data
     * @throws \Exception
     */
    public function error($message = '', $data = [])
    {
        if ($this->request->isAjax()) {
            parent::error($message, $data);
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

    public function redirect($url, $option = [])
    {
        $url = strpos($url, 'http') !== false ? $url : $this->createUrl($url, $option);
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