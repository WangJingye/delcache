<?php

namespace admin\system\service;

use admin\common\service\BaseService;

class MenuService extends BaseService
{

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getList($params, $ispage = true)
    {
        $selector = \Db::table('Menu');
        if (isset($params['status']) && $params['status'] != '') {
            $selector->where(['status' => $params['status']]);
        }
        if (isset($params['name']) && $params['name'] != '') {
            $selector->where('name like "%' . $params['name'] . '%"');
        }
        if (isset($params['url']) && $params['url'] != '') {
            $selector->where('url like "%' . $params['url'] . '%"');
        }
        if ($ispage) {
            return $this->pagination($selector, $params);
        }
        return $selector->findAll();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTopList()
    {
        $rows = $this->getAdminMenus();
        $topList = [];
        foreach ($rows as $v) {
            if ($v['parent_id'] == 0) {
                $childList = $this->getChild($rows, $v['id']);
                foreach ($childList as $child) {
                    if ($child['url'] != '') {
                        $v['url'] = $child['url'];
                        $topList[] = $v;
                        break;
                    }
                }
            }
        }
        return $topList;
    }

    /**
     * @throws \Exception
     */
    public function getAdminMenus()
    {
        $selector = \Db::table('Menu')
            ->where(['status' => 1]);
        if (\App::$user['identity'] == 0) {
            $roleMenus = \Db::table('RoleMenu')->rename('a')
                ->join(['b' => 'RoleAdmin'], 'a.role_id = b.role_id')
                ->field(['a.menu_id'])
                ->where(['b.admin_id' => \App::$user['admin_id']])
                ->findAll();
            $roleMenus = array_column($roleMenus, 'menu_id');
            $selector->where(['id' => ['in', $roleMenus]]);
        }
        return $selector->order('sort desc,create_time asc')
            ->findAll();
    }

    public function getChild($rows, $id)
    {
        $childList = [];
        foreach ($rows as $v) {
            if ($v['parent_id'] == $id) {
                $childList[] = $v;
                $arr = $this->getChild($rows, $v['id']);
                $childList = array_merge($childList, $arr);
            }
        }
        return $childList;
    }

    /**
     * 获取菜单列表
     * @param int $parent_id
     * @param int $i
     * @return array
     * @throws \Exception
     */
    public function getChildMenus($parent_id = 0, $i = 0)
    {
        $types = [];
        if ($parent_id == 0) {
            $types[] = ['id' => $parent_id, 'name' => '顶级目录'];
        }
        $rows = \Db::table('Menu')
            ->where(['parent_id' => $parent_id])
            ->where(['status' => 1])
            ->order('sort desc,create_time asc')
            ->findAll();
        $i++;
        foreach ($rows as $v) {
            $name = str_pad($v['name'], (strlen($v['name']) + $i * 2), '--', STR_PAD_LEFT);
            $types[] = ['id' => $v['id'], 'name' => $name];
            $childTypes = $this->getChildMenus($v['id'], $i);
            $types = array_merge($types, $childTypes);
        }
        return $types;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getLeftList()
    {
        $selector = \Db::table('Menu')
            ->where(['status' => 1]);
        if (\App::$user['identity'] == 0) {
            $roleMenus = \Db::table('RoleMenu')->rename('a')
                ->join(['b' => 'RoleAdmin'], 'a.role_id = b.role_id')
                ->field(['a.menu_id'])
                ->where(['b.admin_id' => \App::$user['admin_id']])
                ->findAll();
            $roleMenus = array_column($roleMenus, 'menu_id');
            $selector->where(['id' => ['in', $roleMenus]]);
        }
        $menuList = $this->getAdminMenus();
        $activeList = $this->getActiveMenu();
        $topList = [];
        $leftList = [];
        foreach ($menuList as $v) {
            if ($v['parent_id'] == 0) {
                if (isset($activeList[$v['id']])) {
                    $top = $v;
                }
                $topList[] = $v;
            }
        }
        foreach ($menuList as $v) {
            if ($v['parent_id'] == $top['id']) {
                $leftList[$v['id']]['item'] = $v;
                $leftList[$v['id']]['list'] = [];
            }
        }
        foreach ($menuList as $v) {
            if (isset($leftList[$v['parent_id']])) {
                $leftList[$v['parent_id']]['list'][] = $v;
            }
        }
        return $leftList;
    }

    /**
     * @param $cmenu
     * @param $menuList
     * @return mixed
     * @throws \Exception
     */
    public function getParent($cmenu, $menuList)
    {
        $menuList[$cmenu['id']] = $cmenu;
        if ($cmenu['parent_id'] == 0) {
            return $menuList;
        }
        $menu = \Db::table('Menu')->where(['id' => $cmenu['parent_id']])->find();
        return $this->getParent($menu, $menuList);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCurrentMenu()
    {
        return \Db::table('Menu')->where(['url' => \App::$request->uri])->find();
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getActiveMenu()
    {
        $menu = $this->getCurrentMenu();
        $activeList = [];
        if ($menu) {
            $activeList = $this->getParent($menu, []);
        }
        return $activeList;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllMethodList($menu_id = 0)
    {
        $arr[] = \App::$config->actionNoLoginList;
        $arr[] = \App::$config->actionWhiteList;
        $actionList = [];
        foreach ($arr as $ar) {
            foreach ($ar as $controller => $actions) {
                foreach ($actions as $action) {
                    if ($action == '*') {
                        $actionList[] = strtolower($controller);
                    }
                    $actionList[] = strtolower($controller . '/' . $action);
                }
            }
        }
        $actionList = array_values(array_unique($actionList));
        $existMethodList = \Db::table('Menu')->field(['url', 'id'])->where('url != ""')->findAll();
        $existMethodList = array_column($existMethodList, 'url', 'id');
        $currentMethod = isset($existMethodList[$menu_id]) ? $existMethodList[$menu_id] : '';
        $uriList = [];
        $moduleList = scandir(APP_PATH);
        $excludeModuleList = ['.', '..', 'install'];
        foreach ($moduleList as $module) {
            if (in_array($module, $excludeModuleList)) {
                continue;
            }
            if (!file_exists(APP_PATH . $module . '/controller')) {
                continue;
            }
            $list = scandir(APP_PATH . $module . '/controller');
            foreach ($list as $v) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if (strpos($v, 'Controller') === false) {
                    continue;
                }
                $controller = str_replace('.php', '', $v);
                $v = 'admin\\' . $module . '\\controller\\' . substr($v, 0, -4);
                $methodList = get_class_methods($v);

                foreach ((array)$methodList as $method) {
                    if (strpos($method, 'Action') === false) {
                        continue;
                    }
                    $c = strtolower(trim(preg_replace('/([A-Z])/', '-$1', substr($controller, 0, -10)), '-'));
                    $a = strtolower(trim(preg_replace('/([A-Z])/', '-$1', substr($method, 0, -6)), '-'));
                    $uri = $module . '/' . $c . '/' . $a;
                    if (($uri == $currentMethod || !in_array($uri, $existMethodList)) && !in_array(strtolower($module . '/' . $c), $actionList) && !in_array(strtolower($uri), $actionList)) {
                        $uriList[$uri] = $uri;
                    }
                }
            }
        }
        return $uriList;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveMenu($data)
    {
        $selector = \Db::table('Menu');
        if (isset($data['id']) && $data['id']) {
            if ($data['id'] == $data['parent_id']) {
                throw new \Exception('不能选择自身作为父级功能');
            }
            $selector->where('id != ' . $data['id']);
        }
        $row = $selector->where('name="' . $data['name'] . '"')->find();
        if ($row) {
            throw new \Exception('标题不能重复');
        }

        if (isset($data['id']) && $data['id']) {
            \Db::table('Menu')->where(['id' => $data['id']])->update($data);
        } else {
            \Db::table('Menu')->insert($data);
        }
    }
}