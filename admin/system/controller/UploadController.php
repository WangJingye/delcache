<?php

namespace admin\system\controller;

use admin\common\controller\BaseController;

class UploadController extends BaseController
{
    public function indexAction()
    {
        if (!empty($_FILES['imgFile'])) {
            $res = $this->parseFile($_FILES['imgFile'], 'common');
            exit(json_encode(['error' => 0, 'url' => $res]));
        }
        exit;
    }
}