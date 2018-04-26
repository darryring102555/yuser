<?php

class Jssdk{
    private $appId; //微信应用ID
    private $appSecret; //微信应用秘钥
    private $access_token_key; //微信access token缓存键
    private $jsapi_ticket_key; //微信jsapi ticket缓存键

    public function __construct() {
        $this->appId = C( 'WEIXIN_APPID' );
        $this->appSecret = C( 'WEIXIN_APPSECRET' );
        $this->access_token_key = C('DATA_KEY.R:WeiXinAccessToken');
        $this->jsapi_ticket_key = C('DATA_KEY.R:WeiXinJsapiTicket');
    }

    /**
     * 获取微信js签名信息
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //$url = "http://e.1905.com$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = randStr(16);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "timestamp" => $timestamp,
            "nonceStr"  => $nonceStr,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**
     * 获取微信js Ticket
     */
    public function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $ticket = S($this->jsapi_ticket_key);
        if (!$ticket) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$accessToken;
            $res = json_decode($this->httpGet($url),true);
            if ($res['ticket']) {
                $ticket = $res['ticket'];
                S( $this->jsapi_ticket_key, $res['ticket'], $res['expires_in']-10);
            }
        }
        return $ticket;
    }

    /**
     * 获取微信 access token
     */
    public function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $access_token = S($this->access_token_key);
        if (!$access_token) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url),true);
            if ($res['access_token']) {
                $access_token = $res['access_token'];
                S( $this->access_token_key,$res['access_token'], $res['expires_in']-10 );
            }
        }
        return $access_token;
    }

    /**
     * http请求
     */
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}
