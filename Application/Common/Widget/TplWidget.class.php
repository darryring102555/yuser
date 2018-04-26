<?php
/**
 * Created by PhpStorm.
 * User: zhangshiping
 * Date: 16-1-14
 * Time: 下午1:42
 * 数据模板渲染
 */

namespace Common\Widget;


use Think\Controller;

class TplWidget extends Controller{
    /**
     * 数据模板渲染
     */
    public function renderer($tpl,$data=array()){
        ob_start();
        $old_html = ob_get_clean(); //之前页面渲染
        ob_start();
        $this->assign($data);
        $this->display($tpl);
        $html = ob_get_clean();
        if($old_html)echo($old_html);
        return $html;
    }



} 