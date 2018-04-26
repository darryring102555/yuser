<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 16-1-15
 * Time: 上午10:22
 */

namespace Cron\Controller;


class UserController extends BaseController{
    /**
     * 清理过期未激活用户
     */
    public function clearUser(){
        $res = D('Common/User','Logic')->clearLose();
        if($res!==true)$this->outPut('f',$res);
        $this->outPut();
    }

} 