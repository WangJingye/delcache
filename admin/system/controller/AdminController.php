<?php

namespace admin\system\controller;


use admin\common\controller\BaseController;
use admin\system\service\AdminService;
use core\Db;
use core\Util;

class AdminController extends BaseController
{
    /** @var AdminService */
    public $adminService;

    public function init()
    {
        $this->adminService = new AdminService();
        parent::init();
    }

    /**
     * @throws \Exception
     */
    public function index()
    {
        $params['page'] = $this->request->getParams('page', 1);
        $params['pageSize'] = $this->request->getParams('pageSize', 10);
        $params['status'] = $this->request->getParams('status');
        $params['search_type'] = $this->request->getParams('search_type');
        $params['search_value'] = $this->request->getParams('search_value');
        if (!empty($params['search_type'])) {
            $params[$params['search_type']] = $params['search_value'];
        }
        /** @var AdminService $res */
        $res = $this->adminService->getList($params);
        $this->assign('params', $params);
        $this->assign('pagination', $this->pagination($res));
        $this->assign('list', $res->list);
    }

    /**
     * @throws \Exception
     */
    public function editAdmin()
    {
        $params = $this->request->params;
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                if (!empty($_FILES['file'])) {
                    $file = $_FILES['file'];
                    $params['avatar'] = $this->parseFile($file);
                }
                $this->adminService->saveAdmin($params);
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        if (isset($params['admin_id']) && $params['admin_id']) {
            $model = Db::table('Admin')->where(['admin_id' => $params['admin_id']])->find();
            if (!$model) {
                throw new \Exception('账号不存在');
            }
            $this->assign('model', $model);
        }
    }

    /**　
     * 账号启用\禁用
     * @throws \Exception
     */
    public function setStatus()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $data = $this->request->params;
                Db::table('Admin')->where(['admin_id' => $data['id']])->update(['status' => $data['status']]);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    public function profile()
    {

    }

    public function changePassword()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $params = $this->request->params;
                $this->adminService->changePassword($this->user, $params);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    public function changeProfile()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $params = $this->request->params;
                if (!empty($_FILES['file'])) {
                    $file = $_FILES['file'];
                    $params['avatar'] = $this->parseFile($file);
                }
                $user = $this->user;
                $params['admin_id'] = $user['admin_id'];
                $params['username'] = $user['username'];
                $this->adminService->saveAdmin($params);
                foreach ($params as $k => $v) {
                    $user[$k] = $v;
                }
                Util::session_set('user', $user);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}