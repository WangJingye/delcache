<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:48 PM
 */

namespace admin\system\service;

use admin\common\service\BaseService;
use common\extend\encrypt\Encrypt;
use core\Config;
use core\Db;
use core\Util;

class AdminService extends BaseService
{
    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getList($params, $ispage = true)
    {
        $selector = Db::table('Admin');
        if (isset($params['status']) && $params['status'] != '') {
            $selector->where(['status' => $params['status']]);
        }
        if (isset($params['admin_id']) && $params['admin_id'] != '') {
            $selector->where(['admin_id' => $params['admin_id']]);
        }
        if (isset($params['username']) && $params['username'] != '') {
            $selector->where('username like "%' . $params['username'] . '%"');
        }
        if (isset($params['realname']) && $params['realname'] != '') {
            $selector->where('realname like "%' . $params['realname'] . '%"');
        }
        if ($ispage) {
            return $this->pagination($selector, $params);
        }
        return $selector->findAll();
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveAdmin($data)
    {
        $selector = Db::table('Admin');
        if (isset($data['admin_id']) && $data['admin_id']) {
            $selector->where('admin_id != ' . $data['admin_id']);
        }
        $row = $selector->where(['username' => $data['username']])->find();
        if ($row) {
            throw new \Exception('用户名不能重复');
        }

        if (isset($data['admin_id']) && $data['admin_id']) {
            Db::table('Admin')->where(['admin_id' => $data['admin_id']])->update($data);
        } else {
            $data['salt'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $password = Config::get('default-password');
            $data['password'] = Encrypt::encryptPassword($password == '' ? '123456' : $password, $data['salt']);
            Db::table('Admin')->insert($data);
        }
    }

    /**
     * @param $admin
     * @param $data
     * @throws \Exception
     */
    public function changePassword($admin, $data)
    {
        if ($admin['password'] != Encrypt::encryptPassword($data['password'], $admin['salt'])) {
            throw new \Exception('当前登录密码有误～');
        }
        if ($data['newPassword'] != $data['rePassword']) {
            throw new \Exception('新密码与验证密码不一致～');
        }
        $update['salt'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $update['password'] = Encrypt::encryptPassword($data['newPassword'], $update['salt']);
        Db::table('Admin')->where(['admin_id' => $admin['admin_id']])->update($update);
        Util::session_unset('user');
    }
}