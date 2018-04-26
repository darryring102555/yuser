<?php
/**
 * Created by PhpStorm.
 * User: 张世平
 * Date: 14-11-14
 * Time: 下午3:50
 */

namespace Common\Behaviors;
use Think\Behavior;

class checkPowerBehavior extends Behavior{

    //允许不用登录页面
    private $allowUrl = array(
        '/Admin/Login/index', //登录页面
        '/Admin/Login/doLogin',
        '/Admin/Index/index'
    );


    //跳转登录后台页面
    private function goLoginAdmin($info = '请重新登录!'){
        $result['code'] = CODE_F;
        $result['msg'] = $info;
        outPut($result);
        jump(U('Admin/Login/index'),'',0);
    }

    //检验后台操作权限,以及登录权限
    public function run(&$param){
        /* 后台自动登录功能 */
        if(MODULE_NAME=='Admin' && !session(C('DATA_KEY.S:Alogin')) && cookie(C('DATA_KEY.C:AautoLogin')) && cookie(C('DATA_KEY.C:Password')) && intval(cookie(C('DATA_KEY.C:Uid')))){
            $loginInfo = D('Common/Login','Logic')->aotoLoginAdmin(intval(cookie(C('DATA_KEY.C:Uid'))),cookie(C('DATA_KEY.C:Password')));
            if($loginInfo!==true && !in_array(U(),$this->allowUrl))$this->goLoginAdmin($loginInfo);
        }

        if (MODULE_NAME == 'Admin') {
            /* 检查用户权限 */
            $p = session(C('DATA_KEY.S:Power'));
            $p = $p ? $p : array();
            if(!in_array(str_replace(C('URL_HTML_SUFFIX'),'',U()),array_merge($p,$this->allowUrl)) && session(C('DATA_KEY.S:Super'))!=1){
                $result['code'] = CODE_I;
                $result['msg'] = '你还没有访问权限,请联系管理员!';
                outPut($result);
                exit('你还没有访问权限,请联系管理员!<a href="/Admin/Login/index.html">登录</a>');
            }
        }
    }
}
