<?php
namespace Admin\Model;


use Common\Model\TreeModel;

class PowerModel extends TreeModel{
    protected $_validate= array(

    );
    protected $_auto = array (
        array('url','trim',3,'function')
    );
    protected $_line = array(
        array('Role','AdminUser','User','AdminUserLog')
    );
    protected $_link = array(
        'Role' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'Role',
            'foreign_key' => 'pid',
            'relation_foreign_key' => 'rid',
            'relation_table' => '__ROLE_POWER__',
            'mapping_name' => 'RolePower',
            'mapping_order' => 'id DESC',
            'mapping_limit' => '20',
            'condition' => '`status`>0',
            'relation_tb' => 'role',
            'relation_alias' => 'r',
            'relation_field' => 'rid',
            'main_table' => 'power',
            'main_field' => 'pid',
            'main_alias' => 'p',
            'middle_table'=>'role_power',
            'middle_alias'=>'pr'
        ));


}