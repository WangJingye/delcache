<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;
use admin\system\service\AdminService;
use common\extend\encrypt\Encrypt;

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
    public function indexAction()
    {
        $params = \App::$request->params;
        $params['page'] = \App::$request->getParams('page', 1);
        $params['pageSize'] = \App::$request->getParams('pageSize', 10);
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
    public function editAdminAction()
    {
        $params = \App::$request->params;
        if (\App::$request->isAjax() && \App::$request->isPost()) {
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
            $model = \Db::table('Admin')->where(['admin_id' => $params['admin_id']])->find();
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
    public function setStatusAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $data = \App::$request->params;
                \Db::table('Admin')->where(['admin_id' => $data['id']])->update(['status' => $data['status']]);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    public function profileAction()
    {

    }

    public function changePasswordAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $params = \App::$request->params;
                $user = \App::$user;
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('当前登录密码有误～');
                }
                if ($params['newPassword'] != $params['rePassword']) {
                    throw new \Exception('新密码与验证密码不一致～');
                }
                $this->adminService->changePassword($user, $params);
                \App::$session->set('user');
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    public function changeProfileAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $params = \App::$request->params;
                if (!empty($_FILES['file'])) {
                    $file = $_FILES['file'];
                    $params['avatar'] = $this->parseFile($file);
                }
                $user = \App::$user;
                $params['admin_id'] = $user['admin_id'];
                $params['username'] = $user['username'];
                $this->adminService->saveAdmin($params);
                foreach ($params as $k => $v) {
                    $user[$k] = $v;
                }
                \App::$session->set('user', $user);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    public function resetPasswordAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $params = \App::$request->params;
                $user = \Db::table('Admin')->where(['admin_id' => $params['admin_id']])->find();
                if (!$user) {
                    throw new \Exception('用户信息有误，请刷新重试');
                }
                $params['newPassword'] = \App::$config->default_password;
                $this->adminService->changePassword($user, $params);
                $this->success('密码已重置');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}