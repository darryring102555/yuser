<?php
namespace Admin\Model;

use Common\Model\BaseModel;

class AdminUserModel extends BaseModel{
    protected $_auto = array();
    protected $_validate= array(
        array('uid','','该用户已是后台管理员！',1,'unique',1), // 在新增时验证
        array('uid','','用户已经存在！',0,'unique',2)
    );
    protected $_line = array(
        array('User','AdminUserLog'),
        array('Role','Power')
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
            'main_table' => 'admin_user',
            'main_field' => 'uid',
            'main_alias' => 'au'
        ), 'Role' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'Role',
            'foreign_key' => 'a_u_id',
            'relation_foreign_key' => 'rid',
            'relation_table' => '__ADMIN_USER_ROLE__',
            'mapping_name' => 'adminUserRole',
            'mapping_order' => 'id DESC',
            'mapping_limit' => '20',
            'condition' => '`status`>0',
            'relation_tb' => 'role',
            'relation_alias' => 'r',
            'relation_field' => 'rid',
            'main_table' => 'admin_user',
            'main_field' => 'a_u_id',
            'main_alias' => 'au',
            'middle_table'=>'admin_user_role',
            'middle_alias'=>'aur'
        ),);


}