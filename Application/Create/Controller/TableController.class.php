<?php
namespace Create\Controller;

use Admin\Controller\BaseController;

class TableController extends BaseController {
    protected $title = "数据表";
    protected $model_name = 'Create/Table';

    /* 编辑显示数据前 */
    protected function editShowBefore(&$data){
        $this->setTitle("数据表信息");
    }





}