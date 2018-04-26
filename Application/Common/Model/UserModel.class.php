<?php
namespace Common\Model;


class UserModel extends BaseModel{
    protected $_validate= array(
        array('uname','','用户名不能是空！',1,'notequal',1), // 在新增时验证
        array('password','6,18','密码长度必须在6-18之间！',1,'length',1), // 在新增时验证
        array('uname','','用户已经存在！',1,'unique',1), // 在新增时验证
        array('name','2,16','姓名长度必须在2-16之间',2,'length',3),
        array('repassword','password','两次输入密码不一致',0,'confirm',3),
        array('email','email','必须是电子邮箱格式',2,'',3),
        array('password','6,18','密码长度必须在6-18之间！',0,'length',2), // 在新增时验证
        array('phone',array(10000000000,19999999999),'必须填写正确的手机号码',2,'between',3),
        array('phone','','该手机号码已注册过了',2,'unique',3),
        array('email','','该邮箱已注册过了',2,'unique',3),
        array('uname','','用户已经存在！',0,'unique',2), //修改时验证
    );
    protected $_auto = array (
        array('status','2'),
        array('password','md5',3,'function') , //对password字段在新增和编辑的时候使md5函数处理
        array('key','randStr',1,'function'), //随机字符串key
        array('photos','json_encode',2,'function'),
        array('photos1','json_encode',2,'function'),
        array('birthday','strtotime',2,'function')
    );
    protected $_line = array();
    protected $_link = array(
        'AdminUser' => array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'AdminUser',
            'foreign_key' => 'uid',
            'as_fields' => null,
            'mapping_name' => 'AdminUser',
            'condition' => '`status`>0',
            'relation_tb' => 'admin_user',
            'relation_alias' => 'au',
            'relation_field' => 'uid',
            'main_table' => 'user',
            'main_field' => 'id',
            'main_alias' => 'u'
        ), 'AdminUserLog' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'AdminUserLog',
            'foreign_key' => 'uid',
            'parent_key' => null,
            'mapping_fields' => null,
            'mapping_order' => 'id DESC',
            'mapping_limit' => '20',
            'mapping_name' => 'AdminUserLog',
            'condition' => '`status`>0',
            'relation_tb' => 'admin_user_log',
            'relation_alias' => 'aul',
            'relation_field' => 'uid',
            'main_table' => 'user',
            'main_field' => 'id',
            'main_alias' => 'u'
        ),);



}