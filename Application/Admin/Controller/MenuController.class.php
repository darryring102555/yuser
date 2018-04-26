<?php
namespace Admin\Controller;

class MenuController extends BaseController {
    protected $title = "菜单管理";
    protected $model_name = 'Admin/Power';

    /* 将节点向上移动 */
    public function moveTop(){
        $this->setAjax();
        $id = intval($this->request['id']);
        $pid = intval($this->request['pid']);
        if($id<1 || $pid<1)$this->outPut('p');
        $near_id = D($this->model_name)->getChilds($pid);
        if (!($id == $near_id[0]['id'] || D($this->model_name)->moveNear($id, $near_id[0]['id']))) {
            $this->outPut('f','置顶失败!');
        }
        $this->outPut('ok','置顶成功!');
    }

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

    /* 编辑显示前 */
    protected function editShowBefore(&$data){
        $this->setTitle("权限");
        $this->assigns('tree',D($this->model_name)->getAllChilds());
    }


}