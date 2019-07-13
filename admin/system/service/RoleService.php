<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:48 PM
 */

namespace admin\system\service;

use admin\common\service\BaseService;
use core\Db;

class RoleService extends BaseService
{
    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getList($params, $ispage = true)
    {
        $selector = Db::table('Role');
        if (isset($params['status']) && $params['status'] != '') {
            $selector->where(['status' => $params['status']]);
        }
        if (isset($params['name']) && $params['name']) {
            $selector->where('name like "%' . $params['name'] . '%"');
        }
        if (isset($params['id']) && $params['id']) {
            $selector->where(['id' => $params['id']]);
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
    public function saveRole($data)
    {
        $selector = Db::table('Role');
        if (isset($data['id']) && $data['id']) {
            $selector->where('id != ' . $data['id']);
        }
        $row = $selector->where('name="' . $data['name'] . '"')->find();
        if ($row) {
            throw new \Exception('角色名称不能重复');
        }

        if (isset($data['id']) && $data['id']) {
            Db::table('Role')->where(['id' => $data['id']])->update($data);
        } else {
            Db::table('Role')->insert($data);
        }
    }

    /**
     * @param $params
     * @throws \Exception
     */
    public function setRoleMenu($params)
    {
        $menuIds = explode(',', $params['menu_ids']);
        $roleMenus = Db::table('RoleMenu')->where(['role_id' => $params['id']])->findAll();
        $roleMenus = array_column($roleMenus, 'id', 'menu_id');
        $addMenuIds = array_diff($menuIds, array_keys($roleMenus));
        $addMenuList = [];
        foreach ($addMenuIds as $key => $menuId) {
            $addMenuList[] = [
                'role_id' => $params['id'],
                'menu_id' => $menuId,
                'create_time' => time()
            ];
        }
        $removeAdminIds = array_diff(array_keys($roleMenus), $menuIds);
        if (count($removeAdminIds)) {
            Db::table('RoleMenu')
                ->where(['role_id' => $params['id']])
                ->where('menu_id in (' . implode(',', $removeAdminIds) . ')')->delete();
        }
        if (count($addMenuList)) {
            Db::table('RoleMenu')->multiInsert($addMenuList);
        }
    }

    /**
     * @param $params
     * @throws \Exception
     */
    public function setRoleAdmin($params)
    {
        $adminIdList = isset($params['admin_id']) ? $params['admin_id'] : [];
        $roleMenus = Db::table('RoleAdmin')->where(['role_id' => $params['id']])->findAll();
        $roleMenus = array_column($roleMenus, 'id', 'admin_id');
        $addAdminIds = array_diff($adminIdList, array_keys($roleMenus));
        $addAdminList = [];
        foreach ($addAdminIds as $key => $adminId) {
            $addAdminList[] = [
                'role_id' => $params['id'],
                'admin_id' => $adminId,
                'create_time' => time()
            ];
        }
        $removeAdminIds = array_diff(array_keys($roleMenus), $adminIdList);
        if (count($removeAdminIds)) {
            Db::table('RoleAdmin')
                ->where(['role_id' => $params['id']])
                ->where('admin_id in (' . implode(',', $removeAdminIds) . ')')->delete();
        }
        if (count($addAdminList)) {
            Db::table('RoleAdmin')->multiInsert($addAdminList);
        }
    }
}