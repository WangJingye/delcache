<?php

namespace component;

class UrlManager extends \ObjectAccess
{
    public function createUrl($uri, $option = [])
    {
        if ($uri == '/') {
            $url = \App::$request->defaultUri;
        } else {
            $res = \App::$request->parseUri($uri);
            $option = array_merge($res['params'], $option);
            $url = '/' . $res['module'] . '/' . $res['controller'] . '/' . $res['action'];
        }
        if (count($option)) {
            $url .= '?' . http_build_query($option);
        }
        return $url;
    }
}