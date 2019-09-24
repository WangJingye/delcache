<?php

namespace api\common\controller;

use component\RestController;

class BaseController extends RestController
{
    public function init()
    {
        $this->getUserByToken();
        parent::init();
    }

    protected function getUserByToken()
    {
        $moduleController = \App::$request->module . '/' . \App::$request->controller;
        $url = $moduleController . '/' . \App::$request->action;
        $actionWhites = \App::$config->action_white_list;

        foreach ((array)$actionWhites as $controller => $actionList) {

            foreach ($actionList as $action) {
                if ($action == '*') {
                    $actionWhiteList[] = $controller;
                } else {
                    $actionWhiteList[] = $controller . '/' . $action;
                }
            }
        }

        $header = \App::$request->header;
        $user = null;
        if (isset($header['token']) && $header['token'] != '') {
            $user = \Db::table('User')->where(['token' => $header['token']])->find();
        }
        if (!in_array($url, $actionWhiteList) && !in_array($moduleController, $actionWhiteList) && !$user) {
            throw new \Exception('未登录', 999);
        }
        if ($user) {
            \App::$user = $user;
        }
    }
}