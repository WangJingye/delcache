<?php

namespace admin\erp\service;

use admin\common\service\BaseService;

class SiteInfoService extends BaseService
{

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveSiteInfo($data)
    {
        if (isset($data['id']) && $data['id']) {
            \Db::table('SiteInfo')->where(['id' => $data['id']])->update($data);
        } else {
            \Db::table('SiteInfo')->insert($data);
        }
    }
}