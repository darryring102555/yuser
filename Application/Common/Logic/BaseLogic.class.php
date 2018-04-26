<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-21
 * Time: 下午6:23
 */

namespace Common\Logic;


abstract class BaseLogic {
    public $unError = ''; //未知错误提示信息
    public function __construct(){
        $this->unError = C('SYS_ERROR_MSG');
    }

} 