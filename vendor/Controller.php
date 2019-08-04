<?php

class Controller
{
    /** @var Controller $this */

    protected $variation = [];

    public function init()
    {
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

    public function __construct()
    {
        $this->init();
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
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ];
        exit(json_encode($result));
    }

    /**
     * @param string $message
     * @param array $data
     */
    public function error($message = '', $data = [])
    {
        $result = [
            'code' => 1,
            'message' => $message,
            'data' => $data,
        ];
        exit(json_encode($result));
    }

}