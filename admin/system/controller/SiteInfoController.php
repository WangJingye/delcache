<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;

class SiteInfoController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    /**
     * @throws \Exception
     */
    public function editAction()
    {
        $params = \App::$request->params->toArray();
        if (\App::$request->isAjax() && \App::$request->isPost()) {
            try {
                $siteInfo = \Db::table('SiteInfo')->find();
                if ($siteInfo) {
                    \Db::table('SiteInfo')->update($params);
                } else {
                    \Db::table('SiteInfo')->insert($params);
                }
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $model = \Db::table('SiteInfo')->find();
        $this->assign('model', $model);
    }
}