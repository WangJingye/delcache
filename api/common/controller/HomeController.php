<?php

namespace api\common\controller;

use component\RestController;

class HomeController extends RestController
{
    public function indexAction()
    {
        $this->success('成功');
    }
}