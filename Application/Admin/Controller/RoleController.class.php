<?php
namespace Admin\Controller;

class RoleController extends BaseController {
    protected $title = "角色管理";
    protected $model_name = 'Admin/Role';

    public function userList(){ /* 查询角色列表 */
        $this->setAjax();
        $id = intval($this->request['id']);
        if(!$id)$this->outPut('f');
        $option['table'] = array(
            trueTab('AdminUserRole')=>'aur',
            trueTab('User')=>'u',
            trueTab('AdminUser')=>'au'
        );
        $option['whereStr'] = 'u.id=au.uid AND au.id=aur.a_u_id AND au.status>0 AND aur.rid='.$id;
        $option['field'] = 'au.id,u.name,u.uname';
        $data['list'] = D('AdminUserRole')->getAll($option);
        $this->outPut($data);


    }


    protected function editSelectLater(&$data){
        if(!$data){
            $this->outPut('on','数据不存在!');
        }


        //查询角色拥有权限
        if($data['id']==1){ //超级管理员
            $options['field'] = 'id AS pid';
            $power_id = D('Admin/Power')->getAll($options);
        }else{
            $power_id = D('RolePower')->where('rid='.$data['id'])->field('pid')->select();
        }
        $data['power_ids'] = twoArrOne($power_id,'pid');
    }


    protected function editShowBefore(&$data){
        $this->setTitle("角色");
        //显示权限列表
        $this->assigns('power',D('Power')->getAllChilds(1));

    }


    protected function doEditSaveLater(&$data){
        if($this->request['id']!=1){ //超级管理员不需要修改
            D()->startTrans();
            D('RolePower')->where('rid='.$this->request['id'])->delete();
            if($this->request['power_ids']){
                $rolePower = oneArrTwo($this->request['power_ids'],'pid','rid',$this->request['id']);
                if(D('RolePower')->addAll($rolePower)===false)$this->outPut('f','修改失败!');
            }
            D()->commit();
        }
    }


    protected function doEditAddBefore(&$save){
        $save = $this->request;
        D()->startTrans();
    }


    protected function doEditAddLater(&$data){
        $rolePower = oneArrTwo($this->request['power_ids'],'pid','rid',$this->request['id']);
        if(D('RolePower')->addAll($rolePower)===false)$this->outPut('f','添加失败!');
        D()->commit();
    }


}