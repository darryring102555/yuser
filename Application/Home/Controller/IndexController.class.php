<?php
namespace Home\Controller;
class IndexController extends BaseController {
    protected $mustVerify = false; //是否需要登录

    public function verifyCode(){ //获取验证码
        $img=new \Think\Verify(array(
            'useCurve'  =>  false,
            'fontSize'  =>  30,
            'length'    =>  4,
        ));
        return $img->entry();
    }


    public function index(){
        $list = array('count'=>'21','data'=>array(array("name"=>'张三'),array("name"=>'李四')));
        $this->outPut(get_defined_vars());
    }

    /*
  * tpl:使用模板
  * dir:生成文件的文件夹
  * file:生成文件的文件名
  */
    protected function createFile($tpl, $dir, $file){ /* 重写用于生成模板 */
        is_dir($dir) or mkdir($dir, 0777, true); //创建文件夹
        if (!is_file($dir . $file)) { //需要生成的目标文件不存在才生成
            ob_start();
            $this->display($tpl); //读取模板
            $view_bat_code = ob_get_clean();
            file_put_contents($dir . $file, $view_bat_code) or $this->outPut('f', $dir . $file . '代码生成失败');
        }
    }



}