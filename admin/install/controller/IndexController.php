<?php

namespace admin\install\controller;

use admin\common\controller\BaseController;
use common\extend\encrypt\Encrypt;

class IndexController extends BaseController
{
    public function init()
    {  parent::init();
        $this->layout('install');
        $this->appendCss('css/install.css');
        if (IS_INSTALL) {
            $this->error('网站已经安装');
        }

    }

    //用户协议
    public function agreementAction()
    {

    }

    /**
     * 系统检测
     */
    public function checkAction()
    {
        $phpVersion = @phpversion();
        $data['system']['php_os'] = ['name' => '操作系统', 'require' => '类UNIX', 'is_ok' => 1, 'value' => PHP_OS, 'min' => '不限制'];
        $data['system']['php_version'] = ['name' => 'PHP版本', 'require' => '>5.6.0', 'is_ok' => 0, 'value' => $phpVersion, 'min' => '5.6.0'];
        if (version_compare(phpversion(), '5.6.0', '>=')) {
            $data['system']['php_version']['is_ok'] = 1;
        }
        if (class_exists('pdo')) {
            $data['module']['pdo'] = ['name' => 'PDO', 'require' => '开启', 'is_ok' => 1, 'value' => '已开启', 'min' => '开启'];
        } else {
            $data['module']['pdo'] = ['name' => 'PDO', 'require' => '开启', 'is_ok' => 0, 'value' => '未开启', 'min' => '开启'];
        }
        $extensions = ['PDO_MYSQL' => 'pdo_mysql', 'CURL' => 'curl', 'GD' => 'gd', 'mbstring' => 'mbstring', 'fileinfo' => 'fileinfo'];
        foreach ($extensions as $key => $extension) {
            $data['module'][$extension] = ['name' => $key, 'require' => '开启', 'is_ok' => 0, 'value' => '未开启', 'min' => '开启'];
            if (extension_loaded($extension)) {
                $data['module'][$extension] = ['name' => $key, 'require' => '开启', 'is_ok' => 1, 'value' => '已开启', 'min' => '开启'];
            }
        }

        if (ini_get('file_uploads')) {
            $data['size']['file_upload'] = ['name' => '附件上传', 'require' => '>2M', 'is_ok' => 1, 'value' => ini_get('upload_max_filesize'), 'min' => '不限制'];
        } else {
            $data['size']['file_upload'] = ['name' => '附件上传', 'require' => '>2M', 'is_ok' => 0, 'value' => '禁止上传', 'min' => '不限制'];
        }

        $this->assign('data', $data);
    }

    public function settingAction()
    {

    }

    public function completeAction()
    {
        if (\App::$request->isPost()) {
            try {
                $params = \App::$request->params;
                $default = [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'username' => 'root',
                    'password' => '',
                    'prefix' => 'tbl_',
                    'charset' => 'utf8mb4',
                    'login_name' => 'admin',
                ];
                foreach ($default as $key => $v) {
                    if (!isset($params[$key]) || $params[$key] == '') {
                        $params[$key] = $v;
                    }
                }
                $this->writeDBConfig($params);
                $this->initSql($params);
                file_put_contents(COMMON_PATH . 'config/install.lock', time());
                $this->success('网站安装成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

        }
    }

    public function writeDBConfig($config)
    {
        $code = "<?php
return [
    // 服务器地址
    'hostname' => '{$config['host']}',
    // 数据库名
    'database' => '{$config['dbname']}',
    // 用户名
    'username' => '{$config['username']}',
    // 密码
    'password' => '{$config['password']}',
    // 端口
    'port' => '{$config['port']}',
    // 数据库编码默认采用utf8
    'charset'  => '{$config['charset']}',
    // 数据库表前缀
    'prefix'   => '{$config['prefix']}',
];";
        file_put_contents(COMMON_PATH . 'config/db.php', $code);
    }

    /**
     * @param $config
     * @throws \Exception
     */
    public function initSql($config)
    {
        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "' . $config['dbname'] . '"';
        $db = new \PDO("mysql:host={$config['host']};port={$config['port']}", $config['username'], $config['password']);

        $ret = $db->query($sql)->fetch();
        if ($ret && count($ret)) {
            throw new \Exception('数据库名称重复，请确认~');
        }
        $sql = 'CREATE DATABASE ' . $config['dbname'] . ' DEFAULT CHARSET ' . $config['charset'] . ' COLLATE ' . $config['charset'] . '_general_ci;';
        $db->exec($sql);
        $db->query('use ' . $config['dbname']);
        $db->query('SET NAMES ' . $config['charset']);
        $sql = file_get_contents(COMMON_PATH . 'config/database.sql');
        $db->exec($sql);
        $salt = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $password = Encrypt::encryptPassword($config['login_password'], $salt);
        $sql = "INSERT INTO `tbl_admin` (`username`, `password`, `realname`, `mobile`, `email`, `avatar`, `salt`, `identity`, `last_login_time`, `passwd_modify_time`, `create_time`, `update_time`, `status`)" .
            "VALUE ('{$config['login_name']}', '{$password}', '超级管理员', '', '{$config['email']}', '', '{$salt}', 1, 0, 0, " . time() . ", " . time() . ", 1);";
        $db->exec($sql);
    }
}