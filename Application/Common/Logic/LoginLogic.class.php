<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 15-11-17
 * Time: 下午8:56
 * 登录逻辑
 */

namespace Common\Logic;


class LoginLogic extends BaseLogic{
    /**
     * 后台登录
     * 参数:uname 用户名(string);password 密码(string);remember 记住密码自动登录标志(bool)
     * 返回:true 登录成功(bool);失败错误提示消息(string)
     */
    public function loginAdmin($uname, $password, $remember = false){
        /* 查询用户信息 */
        $options['field_alias'] = 1; //多表查询标记
        $options['where']['uname']['User'] = array('eq', $uname);
        $options['field'] = array('User' => '*', 'obj' => array('`obj`.`id` AS `aid`,`obj`.`status` AS `astatus`', 'uid'));
        $user = D('Admin/AdminUser')->getOne($options);
        if($user===false)return $this->unError;

        if (!$user) return '用户不存在!';
        if ($user['astatus'] == 2) return '帐号已被禁用!';
        if ($user['status'] != 1) return '帐号未激活或已禁止前端登录!';
        if ($user['password'] != md5($password)) return '密码不正确!';

        /* 查询用户角色 */
        $roles = $this->getRoles($user['aid']);
        if($roles===false)return $this->unError;
        $user['role'] = implode('|', twoArrOne($roles, 'name'));
        $user['role_ids'] = twoArrOne($roles, 'id');

        /* 查询用户权限 */
        $super = $this->isSuper($user['aid']); //判断是否是超级管理员
        if($super===false)return $this->unError;
        $power = array(); //权限url
        if ($super != 1) {
            $power = $this->getPower($user['aid']);
            if ($power === false) return $this->unError; //自动登录失败
        }

        /* 记录登录ip */
        $save['id'] = $user['uid'];
        $save['ip'] = $_SERVER['REMOTE_ADDR'];
        if(D('Common/User')->data($save)->save()===false) return $this->unError;

        /* 登录成功 */
        if ($remember) {
            setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], NOW_TIME + 3600 * 24 * 365, '/');
            cookie(C('DATA_KEY.C:Uid'), $user['uid'],C('AUTO_LOGIN_EXPIRATION')); //用户id
            cookie(C('DATA_KEY.C:Password'), md5(md5($user['password'] . $user['key'])),C('AUTO_LOGIN_EXPIRATION')); //加密密码
            cookie(C('DATA_KEY.C:AautoLogin'), 1,C('AUTO_LOGIN_EXPIRATION')); //后台自动登录标记
        }
        return $this->saveLoginAdminInfo($user,$super,$power);
    }

    /**
     * 后台自动登录功能
     * 参数:uid 用户id;
     *     encPassword 加密密码;
     * 返回:true 登录成功(bool);失败错误提示消息(string)
     */
    public function aotoLoginAdmin($uid,$encPassword){
        /* 查询用户信息 */
        $options['where'] = array('`u`.`id`='.$uid,'`au`.`status`=1','`u`.`status`=1'); //用户状态必须都正常
        $options['table'] = array(trueTab('User')=>'u',trueTab('AdminUser')=>'au');
        $options['field'] = '`au`.`id` AS `aid`,`au`.`uid`,`au`.`status` AS `astatus`,`u`.*';
        $user = D()->table($options['table'])
            ->where('`au`.`uid`=`u`.`id`')
            ->where($options['where'])
            ->field($options['field'])->find();

        if(!$user) return '自动登录出错!用户被禁用或被删除，请重新登录。';
        if($user['ip']!=$_SERVER['REMOTE_ADDR'])  //与上次登录IP不一致时,跳转到登录页面
            return '自动登录出错!登录IP不一致，请重新登录。';
        if(md5(md5($user['password'].$user['key']))!=$encPassword)
            return '自动登录出错!用户密码已修改，请重新登录。';

        /* 查询用户角色 */
        $roles = $this->getRoles($user['aid']);
        $user['role'] = implode('|', twoArrOne($roles, 'name'));
        $user['role_ids'] = twoArrOne($roles, 'id');
        if($user['role']===false) return '自动登录出错!查询用户角色失败，请重新登录。';

        /* 查询用户权限 */
        $super = $this->isSuper($user['aid']); //判断是否是超级管理员
        if ($super === false) return '自动登录出错!查询是否为超级管理员失败，请重新登录。'; //自动登录失败
        $power = array(); //权限url
        if ($super != 1) {
            $power = $this->getPower($user['aid']);
            if ($power === false) return '自动登录出错!查询用户权限失败，请重新登录。'; //自动登录失败
        }

        /* 自动登录成功 */
        return $this->saveLoginAdminInfo($user,$super,$power);
    }

    /**
     * 前台登录
     * 参数:uname 用户名(string);
     *     password 密码(string);
     *     remember 记住密码自动登录标志(bool)
     * 返回:true 登录成功(bool);
     *     失败错误提示消息(string)
     */
    public function loginHome($uname, $password, $remember = false){
        /* 查询用户信息 */
        $options['table'] = array(trueTab('User')=>'u');
        $options['where']['uname'] = array('eq',$uname);
        $user = D()->table($options['table'])->where($options['where'])->find();
        if($user===false) return $this->unError;
        if ($user['status']==2) return '用户还未被激活!';
        if ($user['status']!=1) return '用户不存在!';
        if($user['password']!=md5($password)) return '密码不正确!';

        /* 记录登录IP */
        $save['id'] = $user['id'];
        $save['ip'] = $_SERVER['REMOTE_ADDR'];
        if(D('Common/User')->data($save)->save()===false) return $this->unError;

        /* 登录成功 */
        if($remember){
            setcookie('PHPSESSID',$_COOKIE['PHPSESSID'],NOW_TIME + 3600*24*365,'/');
            cookie(C('DATA_KEY.C:Uid'), $user['id'],C('AUTO_LOGIN_EXPIRATION')); //用户id
            cookie(C('DATA_KEY.C:Password'), md5(md5($user['password'] . $user['key'])),C('AUTO_LOGIN_EXPIRATION')); //加密密码
        }
        return $this->saveLoginHomeInfo($user);
    }

    /**
     * 前台台自动登录功能
     * 参数:uid 用户id;
     *     encPassword 加密密码;
     * 返回:true 登录成功(bool);失败错误提示消息(string)
     */
    public function aotoLoginHome($uid,$encPassword){
        $options['table'] = array(trueTab('User')=>'u');
        $options['where'] = array('id'=>array('eq',$uid),'u.status=1');
        $user = D()->table($options['table'])->where($options['where'])->find();
        if($user===false) return '自动登录出错!查询用户信息失败。';
        if(!$user) return '自动登录出错!用户被禁用或被删除，请重新登录。';
        if($user['ip']!=$_SERVER['REMOTE_ADDR'])
            return '自动登录出错!登录IP不一致，请重新登录。';
        if(md5(md5($user['password'].$user['key']))!=$encPassword)
            return '自动登录出错!用户密码已修改，请重新登录。';
        return $this->saveLoginHomeInfo($user);
    }

    /**
     * 退出登录
     * 返回:true 成功;false 失败
     */
    public function loginOut(){
        /* 登录相关COOKIE */
        cookie(C('DATA_KEY.C:Uid'),null);
        cookie(C('DATA_KEY.C:Password'),null);
        cookie(C('DATA_KEY.C:AautoLogin'),null);

        /* 登录相关SESSION */
        session(C('DATA_KEY.S:Alogin'),null);
        session(C('DATA_KEY.S:Hlogin'),null);
        return !(cookie(C('DATA_KEY.C:Uid')) || cookie(C('DATA_KEY.C:Password'))
            || cookie(C('DATA_KEY.C:AautoLogin')) || session(C('DATA_KEY.S:Hlogin'))
            || session(C('DATA_KEY.S:Alogin'))); //返回退出登录结果
    }


    /**
     * 判断是否是超级管理员
     * 参数:aid 后台用户id;
     * 返回:1-是,2-不是
     */
    public function isSuper($aid){
        $super = D('Admin/AdminUserRole')->where('`rid`=1 AND `a_u_id`=' . $aid)->find(); //判断是否是超级管理员
        if ($super === false) return false; //自动登录失败
        return $super ? 1 : 2; //为1表示是超级管理员
    }

    /**
     * 获取后台用户权限url
     * 参数:aid 后台用户id;
     * 返回:成功:数组;失败:false
     */
    public function getPower($aid){
        $table = array(
            trueTab('power') => 'p',
            trueTab('RolePower') => 'rp',
            trueTab('AdminUserRole') => 'aur',
            trueTab('Role') => 'r'
        );
        $power = D()->where('rp.pid=p.id
                        AND aur.rid=rp.rid
                        AND r.id=aur.rid
                        AND r.status=1
                        AND p.status=1
                        AND aur.a_u_id=' . $aid)
            ->field('url')
            ->table($table)
            ->distinct(true)
            ->select();
        if ($power === false) return false;
        return twoArrOne($power, 'url');
    }

    /**
     * 获取后台用户角色名称
     * 参数:aid 后台用户id;
     * 返回:成功:字符串;失败:false
     */
    public function getRoles($aid){
        $roles = D()->table(array(
            trueTab('AdminUserRole') => 'aur',
            trueTab('Role') => 'r'))
            ->where('aur.rid=r.id AND aur.a_u_id=' . $aid)
            ->field('r.name,r.id')->select();
        return $roles;
    }

    /**
     * 保存后台登录信息
     * 参数:user 用户信息,super 超级管理员标记,power 权限url;
     * 返回:true 成功;false 失败
     */
    private function saveLoginAdminInfo($user,$super,$power){
        session(C('DATA_KEY.S:User'),$user); //用户信息
        session(C('DATA_KEY.S:Super'),$super); //==1为超级管理员标记,用与权限判断
        session(C('DATA_KEY.S:AdminIP'), $_SERVER['REMOTE_ADDR']); //用户ip,防止别人窃取cookie信息登录
        session(C('DATA_KEY.S:Power'), $power); //用户权限url
        session(C('DATA_KEY.S:Alogin'), $user['aid']); //记录登录成功
        return !!session(C('DATA_KEY.S:Alogin')); //返回登录结果
    }

    /**
     * 保存前台登录信息
     * 参数:user 用户信息;
     * 返回:true 成功;false 失败
     */
    private function saveLoginHomeInfo($user){
        $auser = session(C('DATA_KEY.S:User')); //是否后台登录过
        $auser['aid'] OR session(C('DATA_KEY.S:User'),$user); //后台登录已存用户信息时,不覆盖
        session(C('DATA_KEY.S:HomeIP'),$_SERVER['REMOTE_ADDR']);
        session(C('DATA_KEY.S:Hlogin'),$user['id']); //user的id
        return !!session(C('DATA_KEY.S:Hlogin')); //返回登录结果
    }

} 