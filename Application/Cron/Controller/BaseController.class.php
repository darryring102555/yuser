<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 16-1-15
 * Time: 上午10:24
 */

namespace Cron\Controller;


use Common\Controller\CommonController;

class BaseController extends CommonController{
    public function __construct(){
        $this->setAjax();
        set_time_limit(0);
        parent::__construct();
    }
    /* 任务计划权限验证 */
    protected function verify(){
        //if($_SERVER['USER']=='www')$this->outPut('w','你没有权限访问!');
    }

} 