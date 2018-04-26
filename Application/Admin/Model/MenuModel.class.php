<?php
namespace Admin\Model;

use Common\Model\TreeModel;

class MenuModel extends TreeModel{
        protected $_validate= array(

    );
    protected $_auto = array (
                array('ctime','strtotime',2,'function'),            array('utime','strtotime',2,'function'),    );
    protected $_link = array(
    );



}