<?php

namespace admin\system\controller;


use admin\common\controller\BaseController;
use admin\system\service\RoleService;
use core\Db;

class RoleController extends BaseController
{
    /** @var RoleService */
    private $roleService;

    public function init()
    {
        $this->roleService = new RoleService();
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
        /** @var RoleService $res */
        $res = $this->roleService->getList($params);
        $this->assign('params', $params);
        $this->assign('pagination', $this->pagination($res));
        $this->assign('list', $res->list);
    }

    /**
     * @throws \Exception
     */
    public function editRole()
    {
        $params = $this->request->params;
        $id = (int)$this->request->getParams('id', 0);
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $this->roleService->saveRole($this->request->params);
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

        }
        if ($id != 0) {
            $model = Db::table('Role')->where(['id' => $params['id']])->find();
            if (!$model) {
                throw new \Exception('角色不存在');
            }
            $this->assign('model', $model);
        }
    }


    /**
     * 设置用户角色
     * @throws \Exception
     */
    public function setRoleAdmin()
    {
        $params = $this->request->params;
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $this->roleService->setRoleAdmin($params);
                $this->success('设置成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        if (!isset($params['id']) || (int)$params['id'] == 0) {
            throw new \Exception('参数有误');
        }
        $model = Db::table('Role')->where(['id' => $params['id']])->find();
        $rows = Db::table('RoleAdmin')->where(['role_id' => $params['id']])->findAll();
        $adminIdList = array_column($rows, 'admin_id');
        $adminList = Db::table('Admin')->findAll();
        $this->assign('model', $model);
        $this->assign('adminList', $adminList);
        $this->assign('adminIdList', $adminIdList);
    }


    /**
     * 设置角色权限
     * @throws \Exception
     */
    public function setRoleMenu()
    {
        $params = $this->request->params;
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $this->roleService->setRoleMenu($params);
                $this->success('设置成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        if (!isset($params['id']) || (int)$params['id'] == 0) {
            throw new \Exception('参数有误');
        }
        $model = Db::table('Role')->where(['id' => $params['id']])->find();
        $rows = Db::table('RoleMenu')->where(['role_id' => $params['id']])->findAll();
        $roleMenuIds = array_column($rows, 'menu_id');
        $menuList = Db::table('Menu')
            ->field(['id', 'parent_id as pId', 'name'])
            ->where(['status' => 1])
            ->order('sort desc,create_time asc')->findAll();
        foreach ($menuList as $key => $v) {
            if (!empty($roleMenuIds) && in_array($v['id'], $roleMenuIds)) {
                $menuList[$key]['checked'] = true;
            }
        }
        $this->assign('model', $model);
        $this->assign('menuList', $menuList);
    }

}