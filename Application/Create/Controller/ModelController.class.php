<?php
namespace Create\Controller;

use Admin\Controller\BaseController;

class ModelController extends BaseController {
    protected $title = "模型结构";
    protected $model_name = 'Create/TableRelation';
    /* 模型图动态生成的js */
    public function modelGraphJs(){
        //先查询单表
        $this->assign('tables',idToKey(D('Table')->getAll()));
        $this->assign('links',D('table_relation')->getAll());
        $this->assign('ii',-1);
        $this->display();
    }


    public function modelGraph(){
        $this->outPut();
    }
    /* 编辑显示数据前 */
    protected function editShowBefore(&$data){
        $this->setTitle("关系模型");
    }


}