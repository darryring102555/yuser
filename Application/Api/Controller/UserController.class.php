<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-21
 * Time: 下午11:30
 */

namespace Api\Controller;


class UserController extends BaseController{
    protected $title = '欢迎登录';


    /**
     * 前端登录
     * 参数:uname 用户名(string);password 密码(string);remember 记住密码自动登录标志(bool)
     * 返回:true 登录成功(bool);失败错误提示消息(string)
     */
    public function loginHome(){
        if(!$this->post['uname'])$this->outPut('p','用户名不能为空!');
        if(!$_POST['password'])$this->outPut('p','密码不能为空!');
        //if(!check_verify($_REQUEST['verify']))$this->outPut('i','验证码错误!');
        $result = D('Common/Login','Logic')->loginHome($this->post['uname'],$_POST['password'],$this->post['remember']);
        if($result===true)$this->outPut('ok'); //登录成功
        $this->outPut('f',$result); //登录失败
    }


    /**
     * 退出登录
     */
    public function loginOut(){
        $result = D('Common/Login','Logic')->loginOut();
        if($result===true)$this->outPut('ok');
        $this->outPut('f'); //登出失败
    }


    /**
     * 用户注册
     * 参数:uname 用户名(string);password 密码(string);email 电子邮箱(string) 或 手机号
     * 返回:true 注册成功(bool);失败错误提示消息(string)
     */
    public function signIn(){
        if(!$this->post['uname'])$this->outPut('p','用户名不能为空!');
        if(!$_POST['password'])$this->outPut('p','密码不能为空!');
        if($_POST['password']!==$_POST['repassword'])$this->outPut('p','两次密码输入不一致!');
        if(!$this->post['email'] && !$this->post['phone'])$this->outPut('p','请留下联系方式邮箱或者手机号!');

        $result = D('Common/User','Logic')->register($this->post['uname'],$_POST['password'],$this->post['email'],$this->post['phone']);
        if($result===true)$this->outPut('ok'); //注册成功
        $this->outPut('f',$result); //注册失败
    }

    public function changePassword(){
        if(!$this->userInfo['id'])$this->outPut('f','请重新登录!');
        if(!$_POST['password'])$this->outPut('f','请输入新密码!');
        if($_POST['password'] != $_POST['repassword'])$this->outPut('f','两次密码输入不一致!');
        if($this->userInfo['password']!==md5($_POST['old_password']))$this->outPut('f','密码不正确!');
        $data['password'] = $_POST['password'];
        $data['repassword'] = $_POST['repassword'];
        $data['id'] = $this->userInfo['id'];
        $res = D('Common/User')->save($data);
        if($res===false)$this->outPut('f',D('Common/User')->getError());
        $this->userInfo['password'] = md5($_POST['password']);
        session(C('DATA_KEY.S:User'),$this->userInfo);
        $this->outPut('ok','修改密码成功!');
    }


} 