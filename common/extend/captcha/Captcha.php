<?php

namespace common\extend\captcha;
class Captcha
{

    private $width;
    private $height;
    private $codeNum;
    private $image;   //图像资源
    private $disturbColorNum;
    private $checkCode;

    protected $config = [
        'secretKey' => 'delcache',
        // 验证码加密密钥
        'codeSet' => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字符集合
        'expire' => 1800,
        // 验证码过期时间（s）
        'useZh' => false,
        // 使用中文验证码
        'zhSet' => '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借',
        // 中文验证码字符串
        'useImgBg' => false,
        // 使用背景图片
        'fontSize' => 25,
        // 验证码字体大小(px)
        'useCurve' => true,
        // 是否画混淆曲线
        'useNoise' => true,
        // 是否添加杂点
        'imageH' => 0,
        // 验证码图片高度
        'imageW' => 0,
        // 验证码图片宽度
        'length' => 5,
        // 验证码位数
        'fontttf' => '',
        // 验证码字体，不设置随机获取
        'bg' => [243, 251, 254],
        // 背景颜色
        'reset' => true,
        // 验证成功后是否重置
    ];

    function __construct($width = 80, $height = 22, $codeNum = 5)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $number = floor($width * $height / 15);
        if ($number > 240 - $codeNum) {
            $this->disturbColorNum = 240 - $codeNum;
        } else {
            $this->disturbColorNum = $number;
        }
    }

    public function __get($name)
    {
        return $this->config[$name];
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name 配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    //通过访问该方法向浏览器中输出图像
    function showImage($fontFace = "")
    {
        //第一步：创建图像背景
        $this->createImage();
        //第二步：设置干扰元素
        //$this->setDisturbColor();
        //第三步：向图像中随机画出文本
        $this->outputText($fontFace);
        //第四步：输出图像
        $this->outputImage();
    }

    //通过调用该方法获取随机创建的验证码字符串
    function getCheckCode()
    {
        return strtolower($this->checkCode);
    }

    private function createImage()
    {
        //创建图像资源
        $this->image = imagecreatetruecolor($this->width, $this->height);
        //随机背景色
        $backColor = imagecolorallocate($this->image, rand(225, 255), rand(225, 255), rand(225, 255));
        $backColor = imagecolorallocate($this->image, 255, 255, 255);
        //为背景添充颜色
        imagefill($this->image, 0, 0, $backColor);
        //设置边框颜色
        //$border = imagecolorallocate($this->image, 0, 0, 0);
        //画出矩形边框
        //imagerectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $border);
    }

    private function setDisturbColor()
    {
        for ($i = 0; $i < $this->disturbColorNum; $i++) {
            $color = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->image, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }

        for ($i = 0; $i < 10; $i++) {
            $color = imagecolorallocate($this->image, rand(200, 255), rand(200, 255), rand(200, 255));
            imagearc($this->image, rand(-10, $this->width), rand(-10, $this->height), rand(30, 300), rand(20, 200), 55, 44, $color);
        }
    }

    public function createCheckCode()
    {
        $code = "123456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";
        $string = '';
        for ($i = 0; $i < $this->codeNum; $i++) {
            $char = $code{rand(0, strlen($code) - 1)};
            $string .= $char;
        }
        $this->checkCode = $string;
        $key = $this->authcode($this->secretKey);
        $code = $this->authcode(strtoupper($string));
        $secode = [];
        $secode['verify_code'] = $code; // 把校验码保存到session
        $secode['verify_time'] = time(); // 验证码创建时间
        \App::$session->set($key, $secode);
        return $this;
    }

    /**
     * 验证验证码是否正确
     * @access public
     * @param string $code 用户验证码
     * @param string $id 验证码标识
     * @return bool 用户验证码是否正确
     */
    public function check($code)
    {
        $key = $this->authcode($this->secretKey);
        $secode = \App::$session->get($key);
        // 验证码不能为空
        $secode = $secode ? $secode : [];
        if (empty($code) || empty($secode)) {
            return false;
        }
        // session 过期
        if (time() - $secode['verify_time'] > $this->expire) {
            \App::$session->set($key);
            return false;
        }

        if ($this->authcode(strtoupper($code)) == $secode['verify_code']) {
            if ($this->reset) {
                \App::$session->set($key);
            };
            return true;
        }

        return false;
    }

    private function outputText($fontFace = "")
    {
        for ($i = 0; $i < $this->codeNum; $i++) {
            $fontcolor = imagecolorallocate($this->image, rand(0, 128), rand(0, 128), rand(0, 128));
            if ($fontFace == "") {
                $fontsize = rand(12, 16);
                $x = floor($this->width / $this->codeNum) * $i + 3;
                $y = rand(0, $this->height - 15);
                imagechar($this->image, $fontsize, $x, $y, $this->checkCode{$i}, $fontcolor);
            } else {
                $fontsize = rand(12, 16);
                $x = floor(($this->width - 8) / $this->codeNum) * $i + 8;
                $y = rand($fontsize + 5, $this->height);
                imagettftext($this->image, $fontsize, rand(-30, 30), $x, $y, $fontcolor, $fontFace, $this->checkCode{$i});
            }
        }
    }

    private function outputImage()
    {
        if (imagetypes() & IMG_GIF) {
            header("Content-Type:image/gif");
            imagegif($this->image);
        } else if (imagetypes() & IMG_JPG) {
            header("Content-Type:image/jpeg");
            imagejpeg($this->image);
        } else if (imagetypes() & IMG_PNG) {
            header("Content-Type:image/png");
            imagepng($this->image);
        } else if (imagetypes() & IMG_WBMP) {
            header("Content-Type:image/vnd.wap.wbmp");
            image2wbmp($this->image);
        } else {
            die("PHP不支持图像创建");
        }
        imagedestroy($this->_image);
        exit;
    }

    private function authcode($str)
    {
        $key = substr(md5($this->secretKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }

}