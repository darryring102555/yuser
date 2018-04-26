<?php
namespace Admin\Model;

use Common\Model\TreeModel;

class GoodsModel extends TreeModel{
        protected $_validate= array(

    );
    protected $_auto = array (
    array('photos','json_encode',3,'function'),
                        array('time1','strtotime',2,'function'),            array('ctime','strtotime',2,'function'),            array('utime','strtotime',2,'function'),    );
    protected $_link = array(
'Category'=>array(
            'mapping_type'  =>self::BELONGS_TO,
            'class_name'         => 'Category',
                'foreign_key'        => 'category_id',
            'as_fields'          =>null,
            'mapping_name'       =>'Category',
                    'condition'         =>'`status`>0',
            'relation_tb'       => 'category',
            'relation_alias'       => 'ca',
            'relation_field'       =>'id',
            'main_table'         => 'goods',
            'main_field'         =>'category_id',
            'main_alias'         =>'g'
    ),'Brand'=>array(
            'mapping_type'  =>self::BELONGS_TO,
            'class_name'         => 'Brand',
                'foreign_key'        => 'brand_id',
            'as_fields'          =>null,
            'mapping_name'       =>'Brand',
                    'condition'         =>'`status`>0',
            'relation_tb'       => 'brand',
            'relation_alias'       => 'b',
            'relation_field'       =>'id',
            'main_table'         => 'goods',
            'main_field'         =>'brand_id',
            'main_alias'         =>'g'
    ),'Supplier'=>array(
            'mapping_type'  =>self::BELONGS_TO,
            'class_name'         => 'Supplier',
                'foreign_key'        => 'supplier_id',
            'as_fields'          =>null,
            'mapping_name'       =>'Supplier',
                    'condition'         =>'`status`>0',
            'relation_tb'       => 'supplier',
            'relation_alias'       => 's',
            'relation_field'       =>'id',
            'main_table'         => 'goods',
            'main_field'         =>'supplier_id',
            'main_alias'         =>'g'
    ),'Discount'=>array(
            'mapping_type'  =>self::MANY_TO_MANY,
            'class_name'         => 'Discount',
                'foreign_key'        => 'goods_id',
            'relation_foreign_key'  => 'discount_id',
            'relation_table'  => '__GOODS_DISCOUNT__',
            'mapping_name'       =>'GoodsDiscount',
            'mapping_order'      =>'id DESC',
            'mapping_limit'      =>'20',            'condition'         =>'`status`>0',
            'relation_tb'       => 'discount',
            'relation_alias'       => 'd',
            'relation_field'       =>'discount_id',
            'main_table'         => 'goods',
            'main_field'         =>'goods_id',
            'main_alias'         =>'g'
    ),    );



}