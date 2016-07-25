<?php
/**
 * 扫二维码登录
 */
class model_user_scanlogin extends model_user
{
    const URL_WEIXIN_API_USERINFO       = 'https://api.weixin.qq.com/sns/userinfo?';
    const URL_WEIXIN_API_ACCESS_TOKEN   = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    const URL_WEIXIN_API_AUTHORIZE      = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    /**
     * 生产环境
     */
    const APPID                         = 'wx96c9bbe7f9dfc5a4';
    const SECRET                        = '529674a236920a953abc9053434dca43';
    /**
     * 测试环境
     */
//    const APPID                         = 'wx59a99d327dd7c4d1';
//    const SECRET                        = 'd4624c36b6795d1d99dcf0547af5443d';

    const PREFIX_CLIENT                 = 'scanlogin_client_';
    const PREFIX_GRANTINFO              = 'scanlogin_grantinfo_';
    const EXPIRES_LISTEN                = 300; // 秒


    /**
     * 设置监听状态
     */
    public static function setListen()
    {
        S(self::clientId(), '-102', self::EXPIRES_LISTEN);
    }

    /**
     * 设置监听状态为登录
     * @param string $clientId 扫码登录用户唯一标识/-102监听状态/-103待注册状态
     * @param string $sign 加密验证字符串
     */
    public static function setListenLogin($clientId, $sign)
    {
        S($clientId, $sign, self::EXPIRES_LISTEN);
    }

    /**
     * 重置监听状态
     * @param string $clientId 扫码登录用户唯一标识
     */
    public static function resetListen($clientId = null)
    {
        S($clientId ? $clientId : self::clientId(), null);
    }

    /**
     * 获取微信用户基本信息
     * @param string $openid 用户微信ID
     * @param string $accessToken 用户授权token
     * @return array
     */
    public static function userInfo($openid, $accessToken)
    {
        $params = array(
            'access_token' => $accessToken,
            'openid' => $openid,
            'lang' => 'zh_CN'
        );
        $paramsStr = http_build_query($params);
        $url = self::URL_WEIXIN_API_USERINFO . $paramsStr;
        return json_decode(self::apiCurl($url), true);
    }

    /**
     * 获取微信授权code获取用户微信信息
     * @param string $openid 用户微信ID
     * @param string $accessToken 用户授权token
     * @return array
     */
    public static function getUserInfoByCode($code)
    {
        $accessInfo = model_user_scanlogin::accessInfo($code);
        return self::userInfo($accessInfo['openid'], $accessInfo['access_token']);
    }

    /**
     * 获取用户验证信息
     * @param string $code 授权code
     */
    public static function accessInfo($code)
    {
        $params = array(
            'appid' => self::APPID,
            'secret' => self::SECRET,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );
        $paramsStr = http_build_query($params);
        $url = self::URL_WEIXIN_API_ACCESS_TOKEN . $paramsStr;
        return json_decode(self::apiCurl($url), true);
    }

    /**
     * 获取授权code URL
     * @param string $scope 授权类型[snsapi_base、snsapi_userinfo]
     * @param string $clientId 扫码登录用户唯一标识
     * @param string $redirectUrl 回调地址
     */
    public static function grantCodeUrl($scope = 'snsapi_base', $clientId = '', $redirect = null)
    {
        $clientId = $clientId ? $clientId : self::clientId();
        $sign = self::getSign();
        $redirectUrl = url("www-scanlogin/login?client_id={$clientId}&sign={$sign}");
        if ($redirect) {
            $redirectUrl = $redirect;
        }
        $params = array(
            'appid' => self::APPID,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => 'STATE'
        );
        $paramsStr = http_build_query($params);
        return self::URL_WEIXIN_API_AUTHORIZE . $paramsStr . '#wechat_redirect';
    }

    /**
     * 获取app扫码登录需要的url
     * @param string $clientId 扫码登录用户唯一标识
     * @param int $uid 用户ID
     * @return string
     */
    public static function grantAppLoginUrl($uid, $clientId)
    {
        $sign = self::getSign($uid);
        return url("www-scanlogin/login?client_id={$clientId}&sign={$sign}");
    }

    /**
     * 生成登录使用的二维码
     * @param string $type 二维码类型: weixin、app
     * @param string $action 操作类型: login、register、bind
     */
    public static function QR($type = 'weixin', $action = 'login')
    {
        if ($type == 'app') {
            $clientId = self::clientId();
            $content = url("www-scanlogin/login?client_id={$clientId}");
        } else {
            $redirect = '';
            switch ($action) {
                case 'register':
                    $scope = 'snsapi_userinfo';
                    break;
                case 'bind':
                    $scope = 'snsapi_userinfo';
                    $redirect = url("www-bind/bindset?client_id={$clientId}");
                    break;
                default: // 默认登录
                    $scope = 'snsapi_base';
            }
            $content = self::grantCodeUrl($scope, '', $redirect);
        }
        helper_qrcode::png($content, false, 'L', 2);
    }

    /**
     * curl请求
     * @param string $url 请求的地址
     * @param array $postData post提交的参数
     * @param string $method 提交方式
     * @return content
     */
    public static function apiCurl($url, $postData = array(), $method = 'POST')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if ($method == 'POST' ) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }
        $content = curl_exec($curl);
        return $content;
    }

    /**
     * 扫码登录用户唯一标识
     * @return string
     */
    public static function clientId()
    {
        return self::PREFIX_CLIENT . md5(session_id());
    }

    /**
     * 根据扫码登录用户唯一标识获取/设置一个对应的用户微信授权个人基本信息
     * @param string $clientId 扫码登录用户唯一标识
     * @param array $grantInfo 微信授权个人基本信息
     * @return array
     */
    public static function grantInfo($clientId = null, $grantInfo = null)
    {
        $key = self::PREFIX_GRANTINFO . md5($clientId ? $clientId : self::clientId());
        if ($grantInfo) {
            S($key, $grantInfo, 600);
        } else {
            return S($key);
        }
    }

    /**
     * 通过用户ID生成一个sign加密验证字符串
     * @param int $uid 用户ID
     * @return string
     */
    public static function getSign($uid = 0)
    {
        $expires = time() + self::EXPIRES_LISTEN;
        return authcode("{$uid}-{$expires}", 'ENCODE');;
    }

    /**
     * 验证sign返回用户ID
     * @param string $sign 加密验证字符串，格式：authcode(uid + timestamp, 'ENCODE')
     * @return boolean/int
     */
    public static function authuid($sign)
    {
        if ($sign) {
            $info = helper_tool::signdecode($sign);
            if ($info) {
                return $info[0];
            }
            return false;
        }
        return false;
    }

    /**
     * 获取用户绑定信息
     * @param string $openid 用户微信ID
     * @return array
     */
    public static function getUserBindByOpendId($openid)
    {
        $condition = array('openid' => $openid);
        $bind = M('weixin_bind')->where($condition)->find();
        return $bind ? $bind : array();
    }

    /**
     * 用户微信绑定
     * @param string $uid 用户ID
     * @param array $grantInfo 微信授权信息
     * @return array
     */
    public static function weixinBinding($uid, $grantInfo = null)
    {
        if (!$grantInfo) {
            $grantInfo = model_user_scanlogin::grantInfo(model_user_scanlogin::clientId());
        }
        if ($grantInfo) {
            // 检测是否已经绑定过账户
            $bindInfo = model_user_scanlogin::getUserBindByOpendId($grantInfo['openid']);
            if ($bindInfo) {
                return array('status' => 1, 'msg' => '该微信已经绑定过账户');
            }

            // 绑定信息入库
            $bindInfo = array(
                'uid' => $uid,
                'openid' => $grantInfo['openid'],
                'sex' => $grantInfo['sex'],
                'headimgurl' => $grantInfo['headimgurl'],
                'nickname' => $grantInfo['nickname'],
                'province' => $grantInfo['province'],
                'city' => $grantInfo['city'],
                'country' => $grantInfo['country'],
                'add_time' => time(),
                'ip' => getIp()
            );
            $result = M('weixin_bind')->add($bindInfo);
            if ($result) {
                //通过网页用户中心或微信服务号，绑定个人微信号，获得20积分
                model_credit::setCredit(array('uid'=>$uid,'code'=>'wechat_bind'), TRUE);

                return array('status' => 0, 'msg' => '微信绑定成功');
            } else {
                return array('status' => 2, 'msg' => '微信绑定失败');
            }
        }
        return array('status' => 3, 'msg' => '无效的授权信息');
    }

    /**
    * 根据用户ID获取用户已绑定的信息
    * @param int $uid 用户ID
    * @return array
    */
    public function getUserBindByUid($uid)
    {
        $condition = array('uid' => $uid);
        $bind = M('weixin_bind')->where($condition)->find();
        return $bind ? $bind : array();
    }

    /**
    * 微信解绑
    * @param int $uid 用户ID
    * @return boolean
    */
    public function unbind($uid)
    {
        $condition = array('uid' => $uid);
        return M('weixin_bind')->where($condition)->limit(1)->delete();
    }
}
