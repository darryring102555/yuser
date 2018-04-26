<?php
namespace Api\Controller;
use Common\Controller\CommonController;

abstract class BaseController extends CommonController {
    public function __construct(){
        parent::__construct();
        //参数过滤
        $this->request = array_merge($this->get,$this->post);
        $this->setAjax();
    }

    /* 参数处理 */
    protected function handleParams(){
        if(isset($this->get['params'])){
            $this->get = array_merge(I('get.'),$this->filterParam(json_decode($_GET['params'],true)));
        }else{
            $this->get = I('get.');
        }
        if(isset($this->post['params'])){
            $this->post = array_merge(I('post.'),$this->filterParam(json_decode($_POST['params'],true)));
        }else{
            $this->post = I('post.');
        }
    }

    /* 参数过滤 */
    private function filterParam($data){
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 参数过滤
            }
        }
        is_array($data) && array_walk_recursive($data,'think_filter');
        return $data;
    }

    /* api权限验证 */
    protected function verify(){

    }


}

