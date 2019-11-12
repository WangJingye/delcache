<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;

class UploadController extends BaseController
{
    public function indexAction()
    {
        $res = $this->parseFileOrUrl('imgFile', 'common');
        if ($res) {
            exit(json_encode(['error' => 0, 'url' => $res]));
        }
        exit;
    }
}