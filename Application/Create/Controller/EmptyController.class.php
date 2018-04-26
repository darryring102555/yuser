<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-21
 * Time: 上午9:46
 */

namespace Create\Controller;

use Common\Controller\CommonController;

class EmptyController extends CommonController{
    /* 错误访问页面 */
    public function _empty(){
        $this->redirect('Admin/Index/index');
    }
} 