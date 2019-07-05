<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:48 PM
 */

namespace admin\system\service;

use admin\common\service\BaseService;
use common\extend\redis\RedisConnect;
use core\Db;

class MenuService extends BaseService
{

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getList($params, $ispage = true)
    {
        $selector = Db::table('Menu');
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
    public function menus($uri)
    {
        $menuList = Db::table('Menu')->where(['status' => 1])->order('sort desc')->findAll();
        foreach ($menuList as $v) {
            if ($v['parent_id'] == 0) {
                $topList[$v['id']] = $v;
            }
        }
        $leftList = [];
        $leftIds = [];
        foreach ($menuList as $v) {
            if (isset($topList[$v['parent_id']])) {
                $leftList[$v['parent_id']][] = $v;
                $leftIds[] = $v['id'];
            }
        }

        $childList = [];
        $childIds = [];
        foreach ($menuList as $v) {
            if (in_array($v['parent_id'], $leftIds)) {
                $childList[$v['parent_id']][] = $v;
                $childIds[] = $v['id'];
            }
        }
        $endList = [];
        foreach ($menuList as $v) {
            if (in_array($v['parent_id'], $childIds)) {
                $endList[$v['id']] = $v['parent_id'];
            }
        }

        //头部导航设置跳转链接
        foreach ($topList as $key => $v) {
            if (isset($leftList[$v['id']][0]['id'])) {
                $id = $leftList[$v['id']][0]['id'];
                $arr = isset($childList[$id]) ? $childList[$id] : [];
                foreach ($arr as $x) {
                    if ($x['url'] != '') {
                        $topList[$key]['url'] = $x['url'];
                        break;
                    }
                }
            }
        }
        $menuList = array_column($menuList, null, 'id');

        $active = [];
        foreach ($menuList as $id => $v) {
            if (strtolower($uri) == strtolower($v['url'])) {
                if (isset($endList[$v['id']])) {
                    $active = [
                        'childId' => $v['parent_id'],
                        'leftId' => $menuList[$v['parent_id']]['parent_id'],
                        'topId' => $menuList[$menuList[$v['parent_id']]['parent_id']]['parent_id'],
                        'endId' => $v['id'],
                    ];
                } else {
                    $active = [
                        'childId' => $v['id'],
                        'leftId' => $v['parent_id'],
                        'topId' => $menuList[$v['parent_id']]['parent_id'],
                    ];
                }

            }
        }
        return [
            'topList' => $topList,
            'leftList' => $leftList,
            'childList' => $childList,
            'endList' => $endList,
            'menuList' => $menuList,
            'active' => $active
        ];
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
        $redis = RedisConnect::getInstance();
        $key = 'delcache-menu-' . $parent_id . '-' . $i;
//        if (!$redis->exists($key)) {
        $types = [];
        if ($parent_id == 0) {
            $types[] = ['id' => $parent_id, 'name' => '顶级目录'];
        }
        $rows = Db::table('Menu')
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
//            $redis->set($key, json_encode($types), 3600);
//        } else {
//            $types = json_decode($redis->get($key), true);
//        }
        return $types;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllMethodList($menu_id = 0)
    {
        $list = get_declared_classes();


        $baseMethodList = get_class_methods('admin\common\controller\BaseController');
        $existMethodList = Db::table('Menu')->field(['url', 'id'])->where('url != ""')->findAll();
        $existMethodList = array_column($existMethodList, 'url', 'id');
        $currentMethod = isset($existMethodList[$menu_id]) ? $existMethodList[$menu_id] : '';
        $uriList = [];
        foreach (config('module_list') as $module) {
            $list = scandir(APP_PATH . $module . '/controller/');
            foreach ($list as $v) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                $controller = str_replace('.php', '', $v);
                if ($controller == 'BaseController') {
                    continue;
                }
                $v = 'admin\\' . $module . '\\controller\\' . str_replace('.php', '', $v);
                $methodList = get_class_methods($v);
                foreach ($methodList as $method) {
                    if (in_array($method, $baseMethodList)) {
                        continue;
                    }
                    $uri = $module . '/' . strtolower(str_replace('Controller', '', $controller)) . '/' . $method;
                    if ($uri == $currentMethod || !in_array($uri, $existMethodList)) {
                        $uriList[] = $uri;
                    }
                }
            }
        }
        foreach ($list as $v) {
            $arr = explode('\\', $v);
            //不是Controller的命名空间格式
            if (count($arr) != 4 || $arr[0] != 'admin' || $arr[2] != 'controller' || (strpos($arr[3], 'Controller') !== false && strpos($arr[3], 'Controller') !== strlen($arr[3]) - 10)) {
                continue;
            }
            $methodList = get_class_methods($v);
            $methodList = array_diff($methodList, $baseMethodList);
            foreach ($methodList as $method) {
                $uri = $arr[1] . '/' . strtolower(str_replace('Controller', '', $arr[3])) . '/' . $method;
                if ($uri == $currentMethod || !in_array($uri, $existMethodList)) {
                    $uriList[] = $uri;
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
        $selector = Db::table('Menu');
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
            Db::table('Menu')->where(['id' => $data['id']])->update($data);
        } else {
            if (isset($data['id'])) {
                unset($data['id']);
            }
            Db::table('Menu')->insert($data);
        }
    }
}