<?php
namespace Admin\Controller;

class CityController extends BaseController {
    protected $title = "城市管理";
    /* 列表页查询前 */
    protected function indexSelectBefore(&$options){
        $where = $_REQUEST['where'] ? $_REQUEST['where'] : array();
        $order = $_REQUEST['order'] ? $_REQUEST['order'] : array();
        $this->assigns('where',$where);
        $this->assigns('order',$order);
        $options['where'] = $this->request['where'];
        $options['order'] = array_merge(array('lft'=>'ASC'),$order ? $this->request['order']:array());
    }

    /* 编辑查询后 */
    protected function editSelectLater(&$data){
        if(!$data){
            $this->outPut('on','数据不存在!');
        }
        $data['old_parent_id'] = $data['parent_id'];
    }

    /* 编辑显示数据前 */
    protected function editShowBefore(&$data){
        $this->setTitle("地区");
        $this->assigns('tree',D($this->model_name)->getAllChilds());
    }



}