<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 14-11-24
 * Time: 下午10:39
 */

namespace Common\Model;


use Common\Service\NestedSetsService;
use Think\Model;

class TreeModel extends BaseModel{
    protected $nestend='';

    public function __construct(){
        parent::__construct();
        $db = new DbMysqlImpModel();
        $this->nestend = new NestedSetsService($db,$this->trueTableName,'lft','rght','parent_id','id','deep');
    }

    protected $_validate= array(
    );


    public function save(){ /* 修改 */
        $old_parent_id = $this->data['old_parent_id'];
        $parent_id = $this->data['parent_id'];
        unset($this->data['parent_id']);
        $id = $this->data['id'];
        $this->startTrans();
        if(parent::save()===false){
            $this->rollback();
            return false;
        };
        if($parent_id!=$old_parent_id && $old_parent_id){
            //查询验证新parent_id是否是自己的子节点
            $options['where']['id'] = array('eq',$parent_id);
            $options['field'] = 'lft,rght';
            $parent = $this->getOne($options);
            if(!$parent){
                $this->error[] = '移动对象的父节点不存在!';
                return false;
            }
            $options['where']['id'] = array('eq',$id);
            $self = $this->getOne($options);
            if($self['lft']<$parent['lft'] && $self['rght']>$parent['rght']){
                $this->error[] = '不能移动到自己的子节点下!';
                return false;
            }
            $result = $this->nestend->moveUnder($id,$parent_id);
            if($result===false){
                $this->rollback();
                return false;
            }
        }
        return $this->commit();
    }


    public function add(){ /* 添加 */
        $this->create($this->data);
        $data = $this->data;
        $parent_id = $data['parent_id'] ? $data['parent_id'] : 1;
        unset($data['parent_id']);
        unset($data['id']);
        return $this->nestend->insert($parent_id,$data,'bottom');
    }

    /* 删除一条数据或多条 */
    public function removeData($options = array()){
        $options['field'] = 'lft,rght';
        $rows = $this->getAll($options);
        $options = array();
        $flog = 0;
        $this->startTrans();
        foreach($rows as $row){
            $options['where']['lft'] = array('egt',$row['lft']);
            $options['where']['rght'] = array('elt',$row['rght']);
            $res = parent::removeData($options);
            if($res === false){
                $flog = false;
                break;
            }else{
                $flog +=$res;
            }
            parent::removeData($options) === false AND $flog = false;
        }
        $flog!==false ? $this->commit() : $this->rollback();
        return $flog;
    }


    public function getChilds($id,$field){ /* 查询下一级子类 */
        $where['parent_id'] = $id ? $id : 1;
        $where['status'] = 1;
        return $this->field($field)->where($where)->order('lft')->select();
    }


    public function getAllChilds($id,$field){ /* 查询所有子类 */
        if($id){
            $parent = $this->where(array('id'=>$id))->field('lft,rght')->find();
            $where['rght'] = array('lt',$parent['rght']);
            $where['lft'] = array('gt',$parent['lft']);
        }else{
            $where['lft'] = array('gt',0);
        }
        $where['status'] = array('gt',0);
        return $this->field($field)->where($where)->order('lft')->select();
    }


    public function moveNear($id,$near_id,$position = 'before'){ /* 移动到某个节点后或前 */
        $this->startTrans();
        $result = $this->nestend->moveNear($id,$near_id,$position) and $this->commit();
        return $result;
    }



}