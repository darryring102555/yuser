<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-18
 * Time: 下午10:04
 * 用户逻辑
 */

namespace Common\Logic;


class UserLogic extends BaseLogic{
    private $subject = '用户激活'; //注册邮箱通知主题
    private $email_tpl = '../../Common/View/Public/email'; //注册成功邮箱模板
    private $reg_exp = 7200; //注册过期时间

    /**
     * 用户注册
     * 参数:uname 用户名(string);password 密码(string);email 电子邮箱(string) 或 手机号
     * 返回:true 注册成功(bool);失败错误提示消息(string)
     */
    public function register($uname,$password,$email,$phone){
        $data['uname'] = $uname;
        $data['password'] = $password;
        $data['email'] = $email;
        $data['phone'] = $phone;
        $userModel = D('Common/User');
        $userModel->startTrans(); //开启事务
        $uid = $userModel->add($data);
        if($uid===false)return $userModel->getError(); //注册用户成功
        $data['id'] = $uid;
        $option['filed'] = 'key';
        $option['where']['id'] = array('eq',$uid);
        $user = $userModel->getOne($option);
        $data['key'] = md5($email.$user['key']);
        S(C('DATA_KEY.R:ActiveExpires').':'.$data['key'],1,$this->reg_exp); //激活有效时间
        //邮箱通知
        if($email){
            $html = R('Common/Tpl/renderer',array($this->email_tpl,$data),'Widget'); //邮件页面
            if(D('Common/Other','Service')->sendEmail($email,$uname,$this->subject,$html)===false) return '发送邮件通知失败!';
        }
        //短信通知
        if($phone){
            if(!D('Common/Other','Service')->sendSms($phone,'欢迎注册,验证码123') && !$email)return '发送短信通知失败!';
        }
        if(!$userModel->commit())return '创建用户失败!';
        return true; //事务提交
    }

    /**
     * 用户邮箱激活
     * 参数:uid 用户名ID(int);key 激活秘钥(string);
     * 返回:true 注册成功(bool);失败错误提示消息(string)
     */
    public function active($uid,$key){
        if(!S(C('DATA_KEY.R:ActiveExpires').':'.$key)) return '链接已失效!'; //检验激活key是否有效
        $option['filed'] = 'key,email,status';
        $option['where']['id'] = array('eq',$uid);
        $user = D('Common/User')->getOne($option); //查询需要激活的用户信息
        if(!$user)return '链接已失效!'; //用户不存在
        if(md5($user['email'].$user['key'])!=$key)return '链接已失效!'; //检验链接是否正确
        if($user['status']!=2)return '已经成功激活过了!';
        unset($option['filed']);
        if(D('Common/User')->setStatus($option,1)===false)return '激活失败!'; //激活该用户
        S(C('DATA_KEY.R:ActiveExpires').':'.$key,null); //清除标记
        return true; //激活成功
    }

    /**
     * 任务计划,2小时执行一次
     * 删除过期未激活用户
     */
    public function clearLose(){
        $option['where']['status'] = array('eq',2);
        $option['where']['ctime'] = array('lt',NOW_TIME-$this->reg_exp);
        if(D('Common/User')->delete($option)===false)return '清理过期用户失败!';
        return true;
    }

} 