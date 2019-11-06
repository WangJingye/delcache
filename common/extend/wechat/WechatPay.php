<?php

namespace common\extend\wechat;

use component\Util;

class WechatPay
{
    private $config;
    /** @var WechatPay */
    static $instance;

    public static function instance()
    {
        if (static::$instance == null) {
            $instance = new static();
            $config = \App::$config['site_info'];
            $keyList = [
                'wechat_app_id', 'wechat_app_secret', 'wechat_mch_id', 'wechat_pay_key',
            ];
            foreach ($keyList as $key) {
                if (!isset($config[$key]) || !$config[$key]) {
                    throw new \Exception('微信支付相关信息未配置');
                }
            }
            $instance->config = $config;
            static::$instance = $instance;
        }
        return static::$instance;
    }

    /**
     * 统一支付接口
     * data数组需要信息
     *  out_trade_no 支付单号
     *  title 标题
     *  money 金额
     *  type 类型，回调值通过attach判断订单类型
     * @return mixed 小程序需要返回值调起支付
     * @throws \Exception
     */
    public function createPreOrder($title, $out_trade_no, $money, $type = null)
    {
        if ($money == 0) {
            return [];
        }
        if (APP_DEBUG) {
            $money = 0.01;
        }
        $body = $title;// 商品的详情，比如iPhone8，紫色
        $nonce_str = md5($this->config['wechat_app_id'] . time() . rand(10000, 99999));//随机字符串
        $notify_url = \App::$config['site_info']['web_host'] . '/v1/pay/notify';//回调的url【自己填写】';
        $total_fee = (int)($money * 100);//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
        $trade_type = 'JSAPI';//交易类型 默认
        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        $post = [];
        $post['appid'] = $this->config['wechat_app_id'];
        if ($type != null) {
            $post['attach'] = json_encode(['type' => $type]);
        }
        $post['body'] = $body;
        $post['mch_id'] = $this->config['wechat_mch_id'];//你的商户号
        $post['nonce_str'] = $nonce_str;//随机字符串
        $post['notify_url'] = $notify_url;//回调的url
        $post['openid'] = \App::$user['openid'];
        $post['out_trade_no'] = $out_trade_no;//商户订单号
        $post['spbill_create_ip'] = \App::$config['site_info']['web_ip'];//终端的ip
        $post['total_fee'] = $total_fee;//总金额 最低为一块钱 必须是整数
        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post, $this->config['wechat_pay_key']);//签名
        $post_xml = '<xml>
         <appid>' . $this->config['wechat_app_id'] . '</appid>';
        if (isset($post['attach'])) {
            $post_xml .= '<attach>' . $post['attach'] . '</attach>';
        }
        $post_xml .= '<body>' . $body . '</body>
         <mch_id>' . $this->config['wechat_mch_id'] . '</mch_id>
         <nonce_str>' . $nonce_str . '</nonce_str>
         <notify_url>' . $notify_url . '</notify_url>
         <openid>' . \App::$user['openid'] . '</openid>
         <out_trade_no>' . $out_trade_no . '</out_trade_no>
         <spbill_create_ip>' . \App::$config['site_info']['web_ip'] . '</spbill_create_ip>
         <total_fee>' . $total_fee . '</total_fee>
         <trade_type>' . $trade_type . '</trade_type>
         <sign>' . $sign . '</sign>
         </xml>';
        //统一接口prepay_id
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = Util::sendRequest($url, $post_xml);
        /** @var array $res */
        $res = Util::xml2array($xml);
        if (!$res) {
            throw new \Exception('支付返回信息有误');
        }
        $res = $res['xml'];
        if ($res['return_code'] == 'SUCCESS') {
            $time = time();
            $tmp = [];//临时数组用于签名
            $tmp['appId'] = $this->config['wechat_app_id'];
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $res['prepay_id'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";

            $ret['timeStamp'] = "$time";//时间戳
            $ret['nonceStr'] = $nonce_str;//随机字符串
            $ret['signType'] = 'MD5';//签名算法，暂支持 MD5
            $ret['package'] = 'prepay_id=' . $res['prepay_id'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $ret['paySign'] = $this->sign($tmp, $this->config['wechat_pay_key']);//签名,具体签名方案参见微信公众号支付帮助文档;
            $ret['out_trade_no'] = $out_trade_no;
            return $ret;
        } else {
            throw new \Exception($res['return_msg']);
        }
    }

    /**
     * 微信支付回调处理方法
     * @param $xml
     * @throws \Exception
     */
    public function notify($xml)
    {
        $res = Util::xml2array($xml);
        if (!$res) {
            throw new \Exception('交易失败');
        }
        $res = $res['xml'];
        if (!isset($res['return_code']) || $res['return_code'] != 'SUCCESS') {
            throw new \Exception('交易失败');
        } else if (!isset($res['result_code']) || $res['result_code'] != 'SUCCESS') {
            throw new \Exception('交易失败');
        }
        $sign = $res['sign'];
        $newSign = $this->sign($res, $this->config['wechat_pay_key']);
        if ($sign != $newSign) {
            throw new \Exception('签名失败');
        }
        return $res;
    }

    /**
     * 退款
     * @param $order
     * @throws \Exception
     */
    public function refund($order)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $data = [
            'appid' => $this->config['wechat_app_id'],
            'mch_id' => $this->config['wechat_mch_id'],
            'nonce_str' => md5($this->config['wechat_app_id'] . time() . rand(10000, 99999)),
            'transaction_id' => $order['transaction_id'],
            'out_refund_no' => str_replace('BO', 'TK', $order['order_no']),
            'total_fee' => (int)($order['pay_money'] * 100),
            'refund_fee' => (int)($order['pay_money'] * 100),
        ];
        $sign = $this->sign($data, $this->config['wechat_pay_key']);//签名
        $post_xml = '<xml>
         <appid>' . $data['appid'] . '</appid>
         <mch_id>' . $data['mch_id'] . '</mch_id>
         <nonce_str>' . $data['nonce_str'] . '</nonce_str>
         <transaction_id>' . $data['transaction_id'] . '</transaction_id>
         <out_refund_no>' . $data['out_refund_no'] . '</out_refund_no>
         <total_fee>' . $data['total_fee'] . '</total_fee>
         <refund_fee>' . $data['refund_fee'] . '</refund_fee>
         <sign>' . $sign . '</sign>
         </xml>';

        $xml = Util::sendRequest($url, $post_xml);
        $res = Util::xml2array($xml);
        if (!$res) {
            throw new \Exception('推开返回信息有误');
        }
        $res = $res['xml'];
        if ($res['return_code'] != 'SUCCESS') {
            throw new \Exception($res['return_msg']);
        }
    }


    /**
     * 签名
     * @param $data
     * @param $wx_key
     * @return string
     */
    public function sign($data, $wx_key)
    {
        ksort($data);
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        //申请支付后有给予一个商户账号和密码，登陆后自己设置key
        $stringSignTemp = $stringA . '&key=' . $wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        return strtoupper(md5($stringSignTemp));
    }

    /**
     * 生成交易订单号
     */
    public function generateTradeNo($prefix = 'TD')
    {
        return $prefix . date('YmdHis') . str_pad(rand(000000, 999999), 6, '0', STR_PAD_LEFT);
    }

}