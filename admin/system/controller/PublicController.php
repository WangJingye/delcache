<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;
use admin\system\service\AdminService;
use common\extend\captcha\Captcha;
use common\extend\encrypt\Encrypt;
use generate\Generate;

class PublicController extends BaseController
{
    /** @var AdminService */
    public $adminService;

    public function init()
    {
        $this->adminService = new AdminService();
        parent::init();
    }

    public function loginAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $params = \App::$request->params;
                if (!(new Captcha())->check($params['captcha'])) {
                    throw new \Exception('验证码不正确');
                }
                $user = \Db::table('Admin')->where(['username' => $params['username']])->find();
                if (!$user) {
                    throw new \Exception('用户名密码不正确');
                }
                if ($user['status'] == 0) {
                    throw new \Exception('您的账号已禁用，请联系管理员～');
                }
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('用户名密码不正确');
                }
                $user['last_login_time'] = time();
                \Db::table('Admin')->where(['admin_id' => $user['admin_id']])->update($user);
                \App::$session->set('user', $user, 24 * 3600);
                $this->success('登录成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->layout('empty');
    }

    public function logoutAction()
    {
        \App::$session->set('user');
        $this->redirect('system/public/login');
    }

    public function captchaAction()
    {
        (new Captcha())->generate();
    }
}