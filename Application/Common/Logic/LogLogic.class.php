<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-18
 * Time: 下午10:04
 * 日志逻辑
 */

namespace Common\Logic;


class LogLogic extends BaseLogic{
    /**
     * 添加操作日志
     * 参数:json 结果数据json化,code 返回结果状态码,msg 返回结果信息提示
     * 返回值:
     */
    public function addLog($json='',$code=CODE_OK,$msg=''){
        $user = session(C('DATA_KEY.S:User'));
        $data['ctime'] = NOW_TIME;
        $data['ip'] = get_client_ip();
        $data['url'] = U();
        $data['uid'] = $user['uid'];
        $data['get'] = json_encode($_GET);
        $data['post'] = json_encode($_POST);
        $data['return'] = $json;
        $data['code'] = $code;
        $data['msg'] = $msg;
        $data['model'] = MODULE_NAME;
        if($code==CODE_SE){ //系统错误日志
            return $this->addSysLog($data);
        }
        return $this->addAdminUserLog($data);
    }

    //后台操作日志记录
    private function addAdminUserLog($data){
        return D()->table(trueTab('AdminUserLog'))->data($data)->add();
    }

    //系统错误日志记录
    private function addSysLog($data){
        D()->table(trueTab('SystemLog'))->data($data)->add();
    }

} 