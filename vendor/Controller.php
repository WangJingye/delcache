<?php

class Controller extends ObjectAccess
{
    /** @var Controller $this */

    public function init()
    {
        $siteInfo = Db::table('SiteInfo')->find();
        \App::$config['site_info'] = $siteInfo ? $siteInfo : new ObjectAccess();
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
            'code' => 200,
            'message' => $message,
            'data' => empty($data) ? null : $data,
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
            'code' => 400,
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
    public function parseFile($file, $path = '/')
    {
        $ext_arr = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        $fileList = $file['name'];
        if (is_string($file['name'])) {
            $fileList = [$file['name']];
        }
        $tmpList = $file['tmp_name'];
        if (is_string($file['tmp_name'])) {
            $tmpList = [$file['tmp_name']];
        }
        $res = [];
        $path = trim($path, '/') != '' ? trim($path, '/') . '/' : '';
        foreach ($fileList as $i => $f_name) {
            if (!$f_name) {
                continue;
            }
            $arr = explode('.', $f_name);
            $ext = end($arr);
            if (!in_array($ext, $ext_arr)) {
                throw new \Exception('不允许的文件类型,只支持' . implode('/', $ext_arr));
            }
            $filePath = PUBLIC_PATH . 'upload/' . $path;
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $filename = 'upload/' . $path . md5_file($tmpList[$i]) . '.' . $ext;
            if (!file_exists(PUBLIC_PATH . $filename)) {
                if (@!move_uploaded_file($tmpList[$i], PUBLIC_PATH . $filename)) {
                    throw new \Exception('文件保存失败');
                }
            }
            $res[] = App::$config['site_info']['web_host'] . '/' . $filename;
        }
        return implode(',', $res);
    }

    /**
     * @param $key
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function parseFileOrUrl($key, $path = '/')
    {
        $res = [];
        //如果是common目录下的文件需要移动到对应目录
        if ($urlList = \App::$request->params[$key]) {
            if (!is_array($urlList)) {
                $urlList = explode(',', $urlList);
            }
            $baseUrl = \App::$config['site_info']['web_host'];
            foreach ($urlList as $url) {
                if (strpos($url, $baseUrl . '/upload/common') === 0) {
                    $filename = str_replace($baseUrl . '/upload/common/', '', $url);
                    $oldFilePath = PUBLIC_PATH . 'upload/common/';
                    $oldFilename = $oldFilePath . $filename;
                    $newFilePath = PUBLIC_PATH . 'upload/' . $path;
                    $newFilename = $newFilePath . $filename;
                    if (!file_exists($newFilePath)) {
                        mkdir($newFilePath, 0755, true);
                    }
                    if (file_exists($oldFilename)) {
                        copy($oldFilename, $newFilename);
                        unlink($oldFilename);
                    }
                    $res[] = $baseUrl . '/upload/' . $path . $filename;
                } else {
                    $res[] = $url;
                }
            }
        }
        $path = trim($path, '/') != '' ? trim($path, '/') . '/' : '';
        if (!empty($_FILES[$key])) {
            $res[] = $this->parseFile($_FILES[$key], $path);
        }
        return implode(',', $res);
    }
}