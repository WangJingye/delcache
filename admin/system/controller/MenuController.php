<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;
use admin\system\service\MenuService;

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
    public function indexAction()
    {
        $params = \App::$request->params;
        $params['page'] = \App::$request->getParams('page', 1);
        $params['pageSize'] = \App::$request->getParams('pageSize', 10);
        if (!empty($params['search_type'])) {
            $params[$params['search_type']] = $params['search_value'];
        }  /** @var MenuService $res */
        $res = $this->menuService->getList($params);
        $this->assign('params', $params);
        $this->assign('pagination', $this->pagination($res));
        $this->assign('list', $res->list);
    }

    /**
     * @throws \Exception
     */
    public function editMenuAction()
    {
        $params = \App::$request->params;
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $this->menuService->saveMenu($params);
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

        }
        if (isset($params['id']) && $params['id']) {
            $model = \Db::table('Menu')->where(['id' => $params['id']])->find();
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

    /**
     * @throws \Exception
     */
    public function setStatusAction()
    {
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $data = \App::$request->params;
                \Db::table('Menu')->where(['id' => $data['id']])->update(['status' => $data['status']]);
                $this->success('修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}