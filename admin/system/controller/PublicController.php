<?php

namespace admin\system\controller;


use admin\common\controller\BaseController;
use admin\system\service\AdminService;
use common\extend\captcha\Captcha;
use common\extend\encrypt\Encrypt;
use core\Db;

class PublicController extends BaseController
{
    /** @var AdminService */
    public $adminService;

    public function init()
    {
        $this->adminService = new AdminService();
        parent::init();
    }

    public function login()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $params = $this->request->params;
                if (!(new Captcha())->check($params['captcha'])) {
                    throw new \Exception('验证码不正确');
                }
                $user = Db::table('Admin')->where(['username' => $params['username']])->find();
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('用户名密码不正确');
                }
                $user['last_login_time'] = time();
                Db::table('Admin')->where(['admin_id' => $user['admin_id']])->update($user);
                $_SESSION['user'] = $user;
                $this->success('登录成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->layout('empty');
    }

    public function logout()
    {
        unset($_SESSION['user']);
        $this->redirect('system/public/login');
    }

    public function captcha()
    {
        (new Captcha())->generate();
    }
}