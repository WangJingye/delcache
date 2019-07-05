<?php

namespace admin\common\controller;

use admin\system\service\MenuService;
use core\Controller;
use core\Db;

class BaseController extends Controller
{
    public $user;

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->user = isset($_SESSION['user']) ? $_SESSION['user'] : [];
        $this->validateUserGrant();
        parent::init();
    }

    public function pagination($service)
    {
        if ($service->totalPage <= 10) {
            $startPage = 1;
            $endPage = $service->totalPage;
        } else if ($service->page + 4 >= $service->totalPage) {
            $startPage = $service->totalPage - 9;
            $endPage = $service->totalPage;
        } else if ($service->page - 5 <= 0) {
            $startPage = 1;
            $endPage = 10;
        } else {
            $startPage = $service->page - 5;
            $endPage = $service->page + 4;
        }
        $html = '';
        if ($endPage > 1) {
            $params['page'] = $service->page == 1 ? 1 : $service->page - 1;
            $html .= '<div class="pagination-list"><div class="page-container">共' . $service->total . '条 <select name="pageSize" style="margin-left: 1rem;margin-right: .5rem;" id="page-size">';
            foreach ([10, 20, 50, 100] as $i) {
                $html .= '<option value="' . $i . '" ' . ($i == $service->pageSize ? 'selected' : '') . '>' . $i . '</option>';
            }
            $html .= '</select> 条/页</div>' .
                '<ul class="pagination">' .
                '<li class="page-item ' . ($service->page == 1 ? 'disabled' : '') . '">' .
                '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '" aria-label="Previous">' .
                '<span aria-hidden="true">&laquo;</span>' .
                '<span class="sr-only">Previous</span>' .
                '</a></li>';
            $params['page'] = 1;
            $html .= '<li class="page-item ' . ($service->page == 1 ? 'disabled' : '') . '">' .
                '<a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '">首页</a></li>';
            for ($i = $startPage; $i <= $endPage; $i++) {
                $params['page'] = $i;
                $html .= '<li class="page-item ' . ($service->page == $i ? 'active' : '') . '"><a class="page-link" href="' . $this->createUrl($this->request->uri, $params) . '">' . $i . '</a></li>';
            }
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
            $roleMenus = Db::table('RoleMenu')->where(['menu_id' => $menu['id']])->findAll();
            if (!count($roleMenus)) {
                throw new \Exception('您暂无该权限');
            }
            $roleAdmin = Db::table('RoleAdmin')->where(['admin_id' => $this->user['admin_id']])
                ->where('role_id in (' . implode(',', array_column($roleMenus, 'role_id')) . ')')
                ->find();
            if (!$roleAdmin) {
                throw new \Exception('您暂无该权限');
            }
            $this->setCurrentMenu($uri);
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
    private function setCurrentMenu($uri){
        $menus = (new menuService())->menus($this->request->uri);
        $this->assign('menus', $menus);
    }

    public function checkWhiteList($moduleName, $actionName = null)
    {
        $action = [
            'system/public' => ['login', 'logout', 'captcha', 'error'],
        ];

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
}