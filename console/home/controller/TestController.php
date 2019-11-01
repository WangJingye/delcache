<?php

namespace console\home\controller;

use component\ConsoleController;

class TestController extends ConsoleController
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        echo '脚本执行成功' . PHP_EOL;
    }
}