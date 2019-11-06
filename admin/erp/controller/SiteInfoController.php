<?php

namespace admin\erp\controller;

use admin\common\controller\BaseController;
use admin\erp\service\SiteInfoService;

class SiteInfoController extends BaseController
{
    /** @var SiteInfoService */
    public $siteInfoService;

    public function init()
    {
        $this->siteInfoService = new SiteInfoService();
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
                $this->siteInfoService->saveSiteInfo($params);
                $this->success('保存成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $model = \Db::table('SiteInfo')->find();
        $this->assign('model', $model);
    }
}