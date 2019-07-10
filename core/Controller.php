<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/27
 * Time: 9:25 PM
 */

namespace core;

class Controller
{
    /** @var Controller $this */

    protected $variation = [];

    public function init()
    {
    }

    public function __construct()
    {
    }

    public function assign($key, $value)
    {
        $this->$key = $value;
    }

    public function __set($name, $value)
    {
        if (trim($name) != '') {
            $this->variation[$name] = $value;
        }
    }

    public function __get($name)
    {
        return isset($this->variation[$name]) ? $this->variation[$name] : '';
    }

    public function success($message = '', $data = [])
    {
        $result = [
            'errno' => 0,
            'message' => $message,
            'data' => $data,
        ];
        exit(json_encode($result));
    }

    /**
     * @param string $message
     * @param array $data
     * @throws \Exception
     */
    public function error($message = '', $data = [])
    {
        $result = [
            'errno' => 1,
            'message' => $message,
            'data' => $data,
        ];
        exit(json_encode($result));
    }

}