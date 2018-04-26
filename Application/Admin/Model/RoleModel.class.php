<?php
namespace Admin\Model;


use Common\Model\BaseModel;

class RoleModel extends BaseModel{
    protected $_validate = array();
    protected $_auto = array();
    protected $_link = array(
        'AdminUser' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'AdminUser',
            'foreign_key' => 'rid',
            'relation_foreign_key' => 'a_u_id',
            'relation_table' => '__ADMIN_USER_ROLE__',
            'mapping_name' => 'AdminUserRole',
            'mapping_order' => 'id DESC',
            'mapping_limit' => '20', 'condition' => '`status`>0',
            'relation_tb' => 'admin_user',
            'relation_alias' => 'au',
            'relation_field' => 'a_u_id',
            'main_table' => 'role',
            'main_field' => 'rid',
            'main_alias' => 'r'
        ), 'Power' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'Power',
            'foreign_key' => 'rid',
            'relation_foreign_key' => 'pid',
            'relation_table' => '__ROLE_POWER__',
            'mapping_name' => 'RolePower',
            'mapping_order' => 'id DESC',
            'mapping_limit' => '20', 'condition' => '`status`>0',
            'relation_tb' => 'power',
            'relation_alias' => 'p',
            'relation_field' => 'pid',
            'main_table' => 'role',
            'main_field' => 'rid',
            'main_alias' => 'r'
        ),);


}