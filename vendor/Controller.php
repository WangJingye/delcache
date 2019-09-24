<?php

class Controller extends ObjectAccess
{
    /** @var Controller $this */

    public function init()
    {
    }

    public function before()
    {

    }

    public function after()
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


    /**
     * 文件上传处理
     * @param $file
     * @param bool $is_image
     * @return string
     * @throws \Exception
     */
    public function parseFile($file, $is_image = true)
    {
        $ext_arr = [];
        if ($is_image) {
            $ext_arr = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        }
        $arr = explode('.', $file['name']);
        $ext = end($arr);
        if (!in_array($ext, $ext_arr)) {
            throw new \Exception('不允许的文件类型,只支持' . implode('/', $ext_arr));
        }
        $filename = '/upload/system/image/' . md5_file($file['tmp_name']) . '.' . $ext;
        if (!file_exists(PUBLIC_PATH . $filename)) {
            if (@!move_uploaded_file($file['tmp_name'], PUBLIC_PATH . $filename)) {
                throw new \Exception('文件保存失败');
            }
        }
        return $filename;
    }

}