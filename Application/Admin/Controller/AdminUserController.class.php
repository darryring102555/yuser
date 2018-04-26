<?php
namespace Admin\Controller;

class AdminUserController extends BaseController {
    protected $title = "后台用户管理";
    protected $model_name = 'Admin/AdminUser';
    protected function indexSelectBefore(&$options){
        $options['field_alias'] = 1;
        $options['field'] = array(
            'obj'=>'*',
            'Role'=>array('GROUP_CONCAT(r.name SEPARATOR "|") AS rname'),
            'User'=>array('uname','name'));
        $options['group'] = '`obj`.`id`';
        //$options['sql'] = 1;
    }

    /**
     * 后台用户禁用启用切换
     */
    public function swichForbid(){
        $this->setAjax();
        $options = array();
        if(!$this->request['id'])$this->outPut('w','请传入正确ID!');
        if(!is_array($this->request['id']) && !preg_match('/,/',$this->request['id'])){
            $options['where'] = array('id'=>array('eq',$this->request['id']));
        }else{
            $options['where'] = array('id'=>array('in',$this->request['id']));
        }
        $status = intval($this->request['status'])==1 ? 1 : 2;
        D($this->model_name)->setStatus($options,$status)===false AND $this->outPut('f','操作失败!');
        $this->outPut('ok','操作成功!');
    }



    protected function editSelectLater(&$data){
        if(!$data){
            $this->outPut('on','数据不存在!');
        }
        //查询用户对于的角色id
        $data['role_ids'] = twoArrOne(D('AdminUserRole')->where('a_u_id='.$data['id'])->field('rid')->select(),'rid');
    }


    protected function editShowBefore(&$data){
        //查询所有角色
        $this->assigns('roles', D('Role')->field('id,name,description')->where('status>0')->select());
        $this->setTitle("管理员");
    }

    protected function doEditSaveBefore(&$save){
        if(!$this->request['role_ids'])$this->outPut('w','请至少选择一种角色!');
        $save = $this->request;
        D()->startTrans();
    }


    protected function doEditSaveLater(&$data){
        $option['where']['a_u_id'] = array('eq',$this->request['id']);
        if(D('Admin/AdminUserRole')->delete($option)===false)$this->outPut('f','删除旧角色数据失败!'); //删除以前的角色
        $add = oneArrTwo($this->request['role_ids'],'rid','a_u_id',$this->request['id']); //修改角色数据
        D('Admin/AdminUserRole')->addAll($add) !== false AND D()->commit() OR $this->outPut('f','修改角色出错!'); //添加角色成功
    }


    protected function doEditAddBefore(&$save){
        if(!$this->request['role_ids'])$this->outPut('w','请至少选择一种角色!');
        //先验证前端用户是否存在
        $option['where']['id'] = array('eq',$this->request['uid']);
        $option['field'] = 'id';
        if(!D('Common/User')->getOne($option))$this->outPut('f','用户ID错误!');
        $save = $this->request;
        D()->startTrans();
    }


    protected function doEditAddLater(&$data){
        $add = oneArrTwo($this->request['role_ids'],'rid','a_u_id',$data['id']); //修改角色数据
        D('Admin/AdminUserRole')->addAll($add) !== false AND D()->commit() OR $this->outPut('f','修改角色出错!'); //添加角色成功
    }


}