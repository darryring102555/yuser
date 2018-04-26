<?php
namespace Home\Controller;

class LoginController extends BaseController {
    protected $mustVerify = false; //不需要登录
    protected $title = '欢迎登录';
    public function index(){ /* 登录页面 */
        $this->outPut();
    }

    /**
     * 邮箱激活用户
     */
    public function active(){
        $uid = intval($this->request['id']);
        if($uid<=0 || !$this->request['key']){
            $this->outPut('p','链接已失效!');
        };
        $res = D('Common/User','Logic')->active($uid,$this->request['key']);
        if($res!==true)$this->outPut('f',$res);
        $this->outPut('ok','已成功激活!');
    }






}

