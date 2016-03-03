<?php
/**
 * GitHubOAuthService class file.
 *
 * Register application: https://github.com/settings/applications
 *
 * @author magic <ysjheeqg@163.com>
 * @link https://github.com/
 * @license https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=d4dc899578e954cee01339ccf515e4fca43a4874&lang=zh_CN
 */

require_once dirname(dirname(__FILE__)) . '/EOAuth2Service.php';

/**
 * GitHub provider class.
 *
 * @package application.extensions.eauth.services
 */
class WeiXinOAuthService extends EOAuth2Service
{

    protected $name = 'weixin';
    protected $title = 'WeiXin';
    protected $type = 'OAuth';
    protected $jsArguments = array('popup' => array('width' => 900, 'height' => 450));

    protected $client_id = 'wxbcf1efae1a3fe7d0';
    protected $secret = 'db5c6334a068bfc72fa2fa2f8ec60277';
    protected $open_id = '';
    protected $scope = 'snsapi_login';
    protected $providerOptions = array(
        'authorize' => 'https://open.weixin.qq.com/connect/qrconnect',
        'access_token' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        'redirect_uri' => 'http://www.bmob.cn/site/Outsidecallback/service/weixin',
    );

    protected $errorAccessDeniedCode = 'user_denied';

    /*
     * 获取用户数据
     * */
    protected function fetchAttributes()
    {
        $params = array(
            'openid' => $this->open_id,
        );
        $info = (object)$this->makeSignedRequest('https://api.weixin.qq.com/sns/userinfo', array('data' => $params));

        $this->attributes['id'] = $this->open_id;
        $this->attributes['type'] = 1;//用户注册类型 0:本网站 1:微信  2:github
        $this->attributes['nickname'] = $info->nickname;
        $this->attributes['sex'] = $info->sex;
        $this->attributes['language'] = $info->language;
        $this->attributes['country'] = $info->country;    //国家，如中国为CN
        $this->attributes['city'] = $info->city;
        $this->attributes['avatar_url'] = $info->headimgurl;    //用户头像
        $this->attributes['unionid'] = $info->unionid; //用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的unionid是唯一的。
    }

    protected function getTokenUrl($code)
    {
        return $this->providerOptions['access_token'];
    }

    protected function getAccessToken($code)
    {
        $params = array(
            'appid' => $this->client_id,
            'secret' => $this->secret,
            'grant_type' => 'authorization_code',
            'code' => $code,
        );

        $response = $this->makeRequest($this->getTokenUrl($code), array('data' => $params), false);
        $result = json_decode($response, true);
        $this->open_id = $result['openid'];
        return $result['access_token'];
    }

    /**
     * Returns the error info from json.
     *
     * @param stdClass $json the json response.
     * @return array the error array with 2 keys: code and message. Should be null if no errors.
     */
    protected function fetchJsonError($json)
    {
        if (isset($json->error)) {
            return array(
                'code' => $json->error->code,
                'message' => $json->error->message,
            );
        } else {
            return null;
        }
    }

    /**
     * Add User-Agent header
     *
     * @param string $url
     * @param array $options
     * @return cURL
     */
    protected function initRequest($url, $options = array())
    {
        $ch = parent::initRequest($url, $options);
        curl_setopt($ch, CURLOPT_USERAGENT, 'yii-eauth extension');
        return $ch;
    }
}