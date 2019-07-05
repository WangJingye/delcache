<?php

namespace admin\system\controller;


use admin\common\controller\BaseController;
use admin\system\service\MenuService;
use core\Db;

class MenuController extends BaseController
{
    /** @var MenuService */
    public $menuService;

    public function init()
    {
        $this->menuService = new MenuService();
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
        /** @var MenuService $res */
        $res = $this->menuService->getList($params);
        $this->assign('params', $params);
        $this->assign('pagination', $this->pagination($res));
        $this->assign('list', $res->list);
    }

    /**
     * @throws \Exception
     */
    public function editMenu()
    {
        $params = $this->request->params;
        if ($this->request->isAjax() && $this->request->isPost()) {
            try {
                $this->menuService->saveMenu($params);
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

        }
        if (isset($params['id']) && $params['id']) {
            $model = Db::table('Menu')->where(['id' => $params['id']])->find();
            if (!$model) {
                throw new \Exception('菜单不存在');
            }
            $this->assign('model', $model);
        } else {
            $params['id'] = 0;
        }
        $childList = $this->menuService->getChildMenus();
        $methodList = $this->menuService->getAllMethodList($params['id']);
        $this->assign('methodList', $methodList);
        $this->assign('childList', $childList);
    }

}