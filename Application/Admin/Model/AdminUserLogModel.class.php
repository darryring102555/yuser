<?php
namespace Admin\Model;


use Common\Model\BaseModel;

class AdminUserLogModel extends BaseModel{
    protected $_validate= array(

    );
    protected $_auto = array (

    );
    protected $_line = array(
        array('User','AdminUser','Role','Power')
    );
    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'uid',
            'as_fields' => null,
            'mapping_name' => 'User',
            'condition' => '`status`>0',
            'relation_tb' => 'user',
            'relation_alias' => 'u',
            'relation_field' => 'id',
            'main_table' => 'admin_user_log',
            'main_field' => 'uid',
            'main_alias' => 'aul'
        ),);




}