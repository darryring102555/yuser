<?php
namespace Admin\Controller;

class UserController extends BaseController
{
    protected $title = "用户";

    /* 数据模型 */
    protected $model_name = 'Common/User';

    /* 编辑显示数据前 */
    protected function editShowBefore(&$data)
    {
        $this->setTitle("用户");
        $data['photos'] = $data['photos'] ? json_decode($data['photos'], true) : array();
        $data['ctime'] = date('Y-m-d H:i:s', $data['ctime']);
        $data['utime'] = date('Y-m-d H:i:s', $data['utime']);
    }

    protected function doEditSaveBefore(&$save)
    {
        $save = $this->request;
        $save['detail'] = I('request.detail', '', 'stripslashes');
    }


    protected function doEditAddBefore(&$save)
    {
        $save = $this->request;
        $save['detail'] = I('request.detail', '', 'stripslashes');
    }

}