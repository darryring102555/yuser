<?php
/**
 * Created by PhpStorm.
 * User: 张世平
 * Date: 14-11-14
 * Time: 下午3:50
 */

namespace Common\Behaviors;
use Think\Behavior;

class showHtmlBehavior extends Behavior{
    public function run(&$param){
        if(!($_GET['callback']||$_GET['define']||$_GET['script']||$_GET['ajax']|| in_array($_SERVER['HTTP_HOST'],C('DATA_HTTP'))))$this->show();
    }
    private function show(){
       ?><!DOCTYPE html>
<html>
<head>
    <link href="http://static.yuser.cn/css/reset.css" rel="stylesheet">
    <link href="http://static.yuser.cn/css/admin/public/base.css" rel="stylesheet">
    <link href="http://static.yuser.cn/css/<?php echo MODULE_NAME;?>/<?php echo CONTROLLER_NAME;?>/<?php echo ACTION_NAME;?>.css" rel="stylesheet">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" charset="utf-8" data-requirecontext="_" data-requiremodule="jquery" src="http://static.yuser.cn/tooljs/jquery/2.1.4/jquery.min.js"></script>
    <title>欢迎登录-我的网站</title>
</head>
<body>
<div id="main-content">
    <div id="main" class="main">
        <div id="coutent"></div>
    </div>
</div>
<script src="http://data.yuser.cn/<?php echo MODULE_NAME;?>/<?php echo CONTROLLER_NAME;?>/<?php echo ACTION_NAME;?>.js"></script>
<script src="http://static.yuser.cn/tooljs/requirejs/2.1.18/require.min.js"
        data-main="http://static.yuser.cn/App/main.js?time=1448757859"
        module-name="<?php echo MODULE_NAME;?>"
        controller-name="<?php echo CONTROLLER_NAME;?>"
        action-name="<?php echo ACTION_NAME;?>"
        edition="1448757859"
        var-model = "m"
        var-controller = "c"
        var-action = "a"
        id="entrance"
        debug="1"
        hand-data=""
        data-name="data"
        target="coutent"
        defer async="true">
</script>
</body>
</html>
    <?php exit;
    }
}

