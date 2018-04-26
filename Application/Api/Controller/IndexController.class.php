<?php
namespace Api\Controller;
class IndexController extends BaseController{
    protected $mustVerify = false;

    public function index(){
        $this->display();
    }

}