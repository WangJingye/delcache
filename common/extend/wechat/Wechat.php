<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/10/10
 * Time: 4:41 PM
 */

class Wechat
{

    private $config;
    /** @var Wechat */
    static $instance;

    public static function instance()
    {
        if (static::$instance == null) {
            $instance = new static();
            $config = \App::$config->wechat;
            $keyList = [
                'app_id', 'app_secret',
            ];
            foreach ($keyList as $key) {
                if (!isset($config[$key]) || !$config[$key]) {
                    throw new \Exception('微信相关信息未配置');
                }
            }
            $instance->config = $config;
            static::$instance = $instance;
        }
        return static::$instance;
    }

    /**
     * 根据code获取openId
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public function getOpenIdByCode($code)
    {
        $t_url = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $url = sprintf($t_url, $this->config['app_id'], $this->config['app_secret'], $code);
        $apiData = \component\Util::sendRequest($url, [], 'GET');
        if ($apiData) {
            $apiData = json_decode($apiData, true);
        }
        if (empty($apiData) || !isset($apiData['openid'])) {
            throw new \Exception('微信信息注册失败');
        }
        return $apiData['openid'];
    }

}