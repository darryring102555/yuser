<?php
namespace Create\Model;

use Common\Model\BaseModel;

class TableModel extends BaseModel{
    protected $_validate= array(
        array('alias','','别名已经存在！',1,'unique',1),
        array('alias','','别名已经存在！',0,'unique',2),
        array('table','','数据表已经存在！',1,'unique',1),
        array('table','','数据表已经存在！',0,'unique',2),
    );
    protected $_auto = array (

    );



}