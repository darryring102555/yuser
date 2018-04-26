<?php
namespace Home\Controller;
abstract class BaseController extends \Common\Controller\CommonController {
    /* 前台登录检查 */
    protected function verify(){

        /* 自动登录功能 */
        if(!session(C('DATA_KEY.S:Hlogin')) && cookie(C('DATA_KEY.C:Password')) && intval(cookie(C('DATA_KEY.C:Uid')))){
            $loginInfo = D('Common/Login','Logic')->aotoLoginHome(intval(cookie(C('DATA_KEY.C:Uid'))),cookie(C('DATA_KEY.C:Password')));
            if($loginInfo!==true)$this->goLoginHome($loginInfo,CODE_F);
        }

        if(!session(C('DATA_KEY.S:Hlogin')) || session(C('DATA_KEY.S:HomeIP'))!=$_SERVER['REMOTE_ADDR']){
            $this->goLoginHome('请先登录才能访问!');
        }
    }

    //跳转登录后台页面
    protected function goLoginHome($info = '请登录!',$code = CODE_I){
        $result['code'] = $code;
        $result['msg'] = $info;
        outPut($result);
        jump(U('Index/Login/index'),'',0);
    }

}

