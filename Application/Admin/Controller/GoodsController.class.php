<?php
namespace Admin\Controller;

class GoodsController extends BaseController
{
    protected $title = "商品";

    /* 数据模型 */
    protected $model_name = 'Admin/Goods';

    /* 编辑显示数据前 */
    protected function editShowBefore(&$data)
    {
        $this->setTitle("商品");
        $data['photos'] = $data['photos'] ? json_decode($data['photos'], true) : array();
        $this->assigns('tree', D($this->model_name)->getAllChilds());
        $data['checkbox'] = getCheckboxNum($data['checkbox']);
        $data['time1'] = date('Y-m-d H:i:s', $data['time1']);
        $data['ctime'] = date('Y-m-d H:i:s', $data['ctime']);
        $data['utime'] = date('Y-m-d H:i:s', $data['utime']);
    }

    protected function doEditSaveBefore(&$save)
    {
        $save = $this->request;
        $save['aaa'] = I('request.aaa', '', 'stripslashes');
        $save['checkbox'] = checkboxNum($this->request['checkbox']);
    }


    protected function doEditAddBefore(&$save)
    {
        $save = $this->request;
        $save['aaa'] = I('request.aaa', '', 'stripslashes');
        $save['checkbox'] = checkboxNum($this->request['checkbox']);
    }

}