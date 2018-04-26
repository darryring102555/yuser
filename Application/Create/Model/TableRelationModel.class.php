<?php
namespace Create\Model;

use Common\Model\BaseModel;
use Think\Model;

class TableRelationModel extends BaseModel{
    protected $indirect = false;
    protected $_validate= array(
        array('type','checkType','关系类型必须是数字！',0,'callback',3),
        array('indirect_table','checkIndirect','中间表必须是数字,且必须是t_table表内的id字段！',0,'callback',3),
        array('main_table','number','主表ID必须是t_table表内的id字段！',1,'',1),
        array('main_table','number','主表ID必须是t_table表内的id字段!',0,'',2),
        array('relation_table','number','关系表ID必须是t_table表内的id字段！',1,'',1),
        array('relation_table','number','关系表ID必须是t_table表内的id字段!',0,'',2),
        array('relation_table','checkTable','关系表不存在!',0,'callback',3),
        array('main_table','checkTable','主表不存在!',0,'callback',3),
        array('main_field','checkMainField','所填主表关联字段主表中不存在该字段!',1,'callback',1),
        array('main_field','checkMainField','所填主表关联字段主表中不存在该字段!',0,'callback',2),
        array('relation_field','checkRelationField','所填关联表关联字段关联表中不存在该字段!',1,'callback',1),
        array('relation_field','checkRelationField','所填关联表关联字段关联表中不存在该字段!',0,'callback',2)
    );
    protected $_auto = array (

    );


    public function checkType($type){ //检查关联类型,并标记是否是多对多
        $type==4 AND $this->indirect=true;
        return ($type>=1 && $type<=4);
    }


    public function checkIndirect($indirect_table){ //验证中间表
        if($this->indirect) return $this->checkTable($indirect_table);
        return true;
    }


    public function checkTable($table){ //查询表信息
        return !!D('Table')->getOne(array('where'=>array('id'=>array('eq',$table)),'field'=>'id'));
    }


    public function checkMainField($field){ //检查主表是否存在字段
        $table = $this->indirect ? $this->data['indirect_table'] : $this->data['main_table'];
        return $this->checkField($field,$table);
    }


    public function checkRelationField($field){ //检查主表是否存在字段
        $table = $this->indirect ? $this->data['indirect_table'] : $this->data['relation_table'];
        return $this->checkField($field,$table);
    }


    private function checkField($field,$table){ //检查表中字段
        $trueTableName = D('Table')->getOne(array('where'=>array('id'=>array('eq',$table)),'field'=>'table'));
        $trueTableName = trueTab($trueTableName['table']);
        $sql = "SHOW FIELDS FROM {$trueTableName}";
        $result = M()->query($sql);
        foreach($result as $row){
            if($row['field']==trim($field)) return true;
        }
        return false;
    }



}