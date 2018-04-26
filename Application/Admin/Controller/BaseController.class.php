<?php
namespace Admin\Controller;

use Common\Controller\CommonController;
class BaseController extends CommonController{
    protected $model_name = ''; //数据表模型
    public function __construct(){
        parent::__construct();
        $this->model_name = $this->model_name ? $this->model_name : 'Common/'.CONTROLLER_NAME;
        //页面title
        $this->data['title'] = $this->allTitle;
    }

    /* 前端页面显示 */
    protected function shows($data){
        if($this->header){
            $munes = $this->powerList();
            //菜单选中
            foreach($munes as $row){
                $url = explode('/',$row['url']);
                if($url[1].'/'.$url[2]==MODULE_NAME.'/'.CONTROLLER_NAME){
                    $active_id = array($row['parent_id'],$row['id']);
                    break;
                }
            }
        }

        $css = $this->css; //样式
        $title = $this->allTitle;
        $stitacEdition = $this->stitacEdition;
        $user = $this->userInfo;
        $this->assign(get_defined_vars());
        $tpl = $this->header ? 'Public:header' : 'Public:base';
        $this->display($tpl);
        exit;
    }


    /* 登录检查 */
    protected function verify(){
        if(!session(C('DATA_KEY.S:Alogin')) || session(C('DATA_KEY.S:AdminIP'))!=$_SERVER['REMOTE_ADDR']){
            $result = $this->status['i'];
            $result['msg'] = '请先登录!';
            outPut($result);
            jump(U('Login/index'),'',0);
            exit;
        }
    }

    /* 获取菜单列表 */
    private function powerList(){
        //查询对应用户角色权限
        if(!in_array(1,$this->userInfo['role_ids'])){
            $options['field_alias'] = 1;
            $options['where']['id']['r'] = array('IN',$this->userInfo['role_ids']);
            $options['field'] = array(
                'obj'=>array('*'),
                'Role'=>array('id')
            );
            $options['group'] = '`obj`.`id`';
        }
        $options['where']['deep'] = array('lt',3);
        $options['order'] = 'lft ASC';
        return D('Admin/Power')->getAll($options,1);
    }

    /* 列表页面 */
    public function index(){
        $options = array();
        //条件筛选
        $where = $_REQUEST['where'] ? $_REQUEST['where'] : array();
        $order = $_REQUEST['order'] ? $_REQUEST['order'] : array();
        $this->assigns('where',$where);
        $this->assigns('order',$order);
        $options['where'] = $this->request['where'];
        $options['order'] = $this->request['order'];
        $this->indexSelectBefore($options);
        foreach($options['where'] as $k=>$where){ /* 查询条件为空值时,删除查询条件 */
            if(!is_array(current($where))){
                if($where[1]==='' || ($where[1][0]===''&&$where[1][1]==='')){
                    unset($options['where'][$k]);
                }elseif(current($where)=='between'){
                   if(preg_match('/[:-]/',$where[1][0])||preg_match('/[:-]/',$where[1][1])){ //时间格式转换
                       $options['where'][$k][1][0] = strtotime($where[1][0]);
                       $options['where'][$k][1][1] = $where[1][1] ? strtotime($where[1][1]) : NOW_TIME;
                   }
                }
            }else{
                foreach($where as $kk=>$value){
                    if($value[1]==='' || ($value[1][0]===''&&$value[1][1]==='')){
                        unset($options['where'][$k][$kk]);
                    }elseif(current($value)=='between'){
                        if(preg_match('/[:-]/',$value[1][0])||preg_match('/[:-]/',$value[1][1])){ //时间格式转换
                            $options['where'][$k][$kk][1][0] = strtotime($value[1][0]);
                            $options['where'][$k][$kk][1][1] = $where[1][1] ? strtotime($value[1][1]) : NOW_TIME;
                        }
                    }
                }
            }
        }
        $data = D($this->model_name)->getLimit($this->page,$options);
        if($data===false)$this->outPut('un'); //查询失败
        $this->indexSelectLater($data);
        $this->outPut($data); //结果输出
    }


    public function edit(){ /* 用户编辑,添加页面 */
        $id = intval($this->request['id']);
        $data = array();
        if($id>0){
            $options = array();
            $this->editSelectBefore($options);
            $data = D($this->model_name)->getOne($options);
            $this->editSelectLater($data);
        }
        $this->editShowBefore($data);
        $this->assigns('data',$data);
        $this->outPut();
    }


    public function doEdit(){ /* 用户操作 */
        $this->setAjax();
        $id = intval($this->request['id']);
        $data = array();
        if($id>0){
            $this->doEditSaveBefore($save);
            if(D($this->model_name)->data($save)->save()===false){ //修改失败
                $this->outPut('f',D($this->model_name)->getError());
            }
            $this->doEditSaveLater($data);
            $msg = '修改成功!';
        }else{
            $this->doEditAddBefore($save);
            $data['id'] = D($this->model_name)->data($save)->add();
            if($data['id']===false){
                $this->outPut('f',D($this->model_name)->getError());
            }
            $this->doEditAddLater($data);
            $msg = '添加成功!';
        }
        $this->outPut($data,$msg);
    }


    public function remove(){
        $this->setAjax();
        $options = array();
        $data = array(); //返回结果数据
        if(!$this->request['id'])$this->outPut('w','请传入正确ID!');
        if(!is_array($this->request['id']) && !preg_match('/,/',$this->request['id'])){
            $options['where'] = array('id'=>array('eq',$this->request['id']));
        }else{
            $options['where'] = array('id'=>array('in',$this->request['id']));
        }
        $this->removeBefore($options);
        D($this->model_name)->removeData($options)===false AND $this->outPut('f','删除失败!');
        $this->removeLater($data);
        $this->outPut('ok','删除成功!');
    }


    protected function editSelectBefore(&$options){
        $options['where'] = array('id'=>intval($this->request['id']));
    }


    protected function editShowBefore(&$data){

    }


    protected function editSelectLater(&$data){
        if(!$data){
            $this->outPut('on','数据不存在!');
        }
    }


    protected function indexSelectBefore(&$options){

    }


    protected function indexSelectLater(&$data){

    }

    protected function doEditSaveBefore(&$save){
        $save = $this->request;
    }


    protected function doEditSaveLater(&$data){

    }


    protected function doEditAddBefore(&$save){
        $save = $this->request;
    }


    protected function doEditAddLater(&$data){

    }


    protected function removeBefore(&$options){
    }


    protected function removeLater(&$data){

    }

}

