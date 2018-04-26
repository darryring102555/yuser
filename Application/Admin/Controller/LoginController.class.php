<?php
namespace Admin\Controller;

class LoginController extends BaseController {
    protected $mustVerify = false; //不需要登录
    protected $title = '欢迎登录';

    public function index(){ /* 登录页面 */
        $this->header = false;
        $this->stitacEdition = time();
        $this->css[] = C('TMPL_PARSE_STRING.__JS__').MODULE_NAME.'/'.CONTROLLER_NAME.'/css/'.ACTION_NAME;
        //$this->css[] = C('TMPL_PARSE_STRING.__JS__').'Admin/Public/square/blue';
        $this->outPut();
    }


    public function doLogin(){ /* 执行登录操作 */
        $this->setAjax();
        if(!$this->post['uname'])$this->outPut('p','用户名不能为空!');
        if(!$_POST['password'])$this->outPut('p','密码不能为空!');
        //if(!check_verify($_REQUEST['verify']))$this->outPut('i','验证码错误!');
        $result = D('Common/Login','Logic')->loginAdmin($this->post['uname'],$_POST['password'],$this->post['remember']);
        if($result===true)$this->outPut('ok'); //登录成功
        $this->outPut('f',$result); //登录失败
    }




}

