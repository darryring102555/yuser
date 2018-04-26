<?php
/**
 * 不变动配置
 * Created by zhangshiping.
 * QQ: 214986304
 * Date: 2015-11-16
 * Time: 12:07
 */

/* 返回状态码定义 */
define(CODE_W,3); //警告信息
define(CODE_I,2); //提示信息
define(CODE_P,1); //参数错误
define(CODE_OK,0); //操作成功
define(CODE_F,-1); //操作失败
define(CODE_NO,-2); //没有数据
define(CODE_SE,-3); //系统错误

return array(
    'URL_MODEL' => '2', //URL模式
    'URL_HTML_SUFFIX' => '', //url地址生成后缀

    "PAGE_SIZE" =>  20, //数据展示条数
    'SELF_ERROR' => false, //错误输出自己结果
    'SYS_ERROR_MSG'=>'系统错误,请稍后再试!',//系统错误提示


    /* 数据前缀 */
    'DB_PREFIX'             =>  't_',          //数据库表前缀
    'SESSION_PREFIX'        =>  'testS_',      // Session前缀 避免冲突
    'COOKIE_PREFIX'         =>  'testC_',      // Cookie前缀 避免冲突

    /* 缓存数据key */
    'DATA_KEY'              => array(
        /* session键 */
        'S:Alogin'=>'isLogonAdmin', //判断后台用户是否登录标记(admin_user id)
        'S:User'=>'userInfo', //保存用户信息
        'S:Power'=>'power', //保存用户权限url
        'S:Super'=>'isSuper', //等于1表示为超级管理员,2则不是
        'S:AdminIP'=>'ipAdmin', //保存用户后台登录IP,防止盗用cookie信息登录
        'S:Hlogin'=>'isLogon', //判断前台用户是否登录标记(user id)
        'S:HomeIP'=>'ip', //保存用户前台登录IP,防止盗用cookie信息登录
        /* cookie键 */
        'C:Uid'=>'uid', //保存用户id,用于自动登录
        'C:Password'=>'autoLogin', //用户加密后密码,用于自动登录
        'C:AautoLogin'=>'autoLoginAdmin', //后台自动登录标记
        /* redis键 */
        'R:WeiXinAccessToken'=>'nVendor:Wechat:Jssdk:getAccessToken', //微信的 Access Token
        'R:WeiXinJsapiTicket'=>'nVendor:Wechat:Jssdk:getJsApiTicket', //微信的 Jsapi Ticket
        'R:ActiveExpires'=>'nCommon:Logic:User:register', //用户注册激活过期时间
        'R:ChacheCreate'=>'nCommon:Service:Cache' //模块缓存数据创建标记
    ),
    'AUTO_LOGIN_EXPIRATION'=>3600*24*30*3, //自动登录过期时间

    /* 系统邮件相关 */
    'SMTP_SERVERS'=>'smtp.126.com',//SMTP servers 邮箱服务器地址
    'SMTP_USERNAME'=>'zsping1989',// SMTP username 邮箱用户名  注意：普通邮件认证不需要加 @域名
    'SMTP_PASSWORD'=>'zsping14141414',// SMTP password 邮箱密码
    'SMTP_EMAIL'=>'zsping1989@126.com',// 发件人邮箱
    'SMTP_NAME'=>'张世平',// 发件人姓名
    'EMAIL_REPLY'=>array( //用户回复邮件到该邮箱
        array('email'=>'zsping1989@126.com','name'=>'张世平')

    ),
    /* 手机短信通知配置 */
    'SMS_UNAME'=>'zsping1989', //发送短信接口账号
    'SMS_PASSWORD'=>'14141414', //发送短信接口密码
    'SMS_URL'=>'http://sms.106jiekou.com/utf8/sms.aspx', //发送短信接口地址

    /* 微信公众号配置 */
    'WEIXIN_APPID' => 'wx0b141f8793af7a7a', //微信应用ID
    'WEIXIN_APPSECRET' => '84fec88e7ff5c99d7594c6bcafa5109a', //微信应用秘钥


    /* url相关 */
    'DATA_HTTP'=>array(  //返回json数据url--只提供json数据服务器
        'data.'.HOST
    ),
    'ALLOW_HTTP'=>array(HOST,''), //允许请求数据域名
    'TMPL_PARSE_STRING' => array(
        '__CSS__' =>   STATIC_URL."css/",  //css文件
        '__IMG__' =>   STATIC_URL."img/", //图片文件
        '__TOOLJS__' =>   STATIC_URL."tooljs/", //三方插件js
        '__JS__' =>  APP_DEBUG ? STATIC_URL."debug/" : STATIC_URL."src/" ,     //应用js
        '__APP__' => STATIC_URL //app路径
    ),

);