<?php

namespace admin\system\controller;


use admin\common\controller\BaseController;
use admin\system\service\AdminService;
use common\extend\captcha\Captcha;
use common\extend\encrypt\Encrypt;
use core\Db;
use core\Util;

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
        Db::table('Admin')->insert([]);
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $params = $this->request->params;
                if (!(new Captcha())->check($params['captcha'])) {
                    throw new \Exception('验证码不正确');
                }
                $user = Db::table('Admin')->where(['username' => $params['username']])->find();
                if ($user['status'] == 0) {
                    throw new \Exception('您的账号已禁用，请联系管理员～');
                }
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('用户名密码不正确');
                }
                $user['last_login_time'] = time();
                Db::table('Admin')->where(['admin_id' => $user['admin_id']])->update($user);
                Util::session_set('user', $user,3600);
                $this->success('登录成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->layout('empty');
    }

    public function logout()
    {
        Util::session_unset('user');
        $this->redirect('system/public/login');
    }

    public function captcha()
    {
        (new Captcha())->generate();
    }
}