<?php
function dump_exit($var){ //打印并退出
    header('Content-Type: text/html; charset=utf-8');
    $args = func_get_args();
    if(count($args)==1) $args = $args[0];
    dump($args, $echo=true, $label=null, $strict=true);exit;
}

function check_verify($code, $id = ''){ //验证验证码
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}


function jump($url='',$msg='',$time=3){ //跳转方法
    if(!headers_sent()&&$time==0){
        header("Location: $url");
    }else{
        if(empty($url)){
            $str =  <<<JS
             <script type='text/javascript'>
                window.setTimeout(function(){
                     history.back(-1)
                 },{$time}000)
             </script>
JS;
        }else{
            $str =  <<<JS
             <script type='text/javascript'>
                 window.setTimeout(function(){
                    location.href='{$url}'
                 },{$time}000)
             </script>
JS;
        }
        if($time!==0)$str.=$msg;
        echo $str;
    }
    exit;
}


function lower($str){ /* 将单词转换成有下划线的小写 */
    $res = preg_replace('/([A-Z])/','_$1',$str);
    $res = preg_split('/_/',$res,false,1);
    $res = implode('_',$res);
    return strtolower($res);
}


function upper($str){ /* 把有下划线的单词大写 */
    if(strrpos($str, '_')){
        $str = strtolower($str);
    }
    $str = explode('_',$str);
    array_walk($str,function(&$v,$k){$v = ucfirst($v);});
    return implode('',$str);
}


function randStr($len=4) { /* 获取随机字符串 */
    $chars='ABDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#%*';
    mt_srand((double)microtime()*1000000*getmypid());
    $password='';
    while(strlen($password)<$len)
        $password.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $password;
}


function trueTab($tab){ /* 转换为数据表的真实名称 */
   return $tab ? C('DB_PREFIX').lower($tab) : false;
}


function filter($str){ /* sql过滤 */
    return $str ? str_replace(' ','',htmlspecialchars(strip_tags($str))) : false;
}


function twoArrOne($twoArr,$key){ /* 二维数组转换成一唯数组 */
    if(!$twoArr || !$key)return array();
    foreach($twoArr as $row){
        $result[] = $row[$key];
    }
    return $result;
}


function oneArrTwo($oneArr,$key,$key1,$value){ /* 一唯数组拼接成二维数组 */
    $result = array();
    foreach($oneArr as $k=>$v){
        $result[$k][$key] = $v;
        $result[$k][$key1] = $value;
    }
    return $result;
}

function idToKey($list){ /* 将列表数据的id设成key */
    $result = array();
    foreach($list as $row){
        $result[$row['id']] = $row;
    }
    return $result;
}


function cutOut($str,$len){ /* 中文字符串剪切 */
    $result = mb_substr($str,0,$len,'utf-8');
    mb_strlen($str,'UTF8')>$len AND $result .='...';
    return $result;
}


function timeDiff($start,$end = false,$format=false,$max='default'){ /* 计算两点时间差,相聚多久 */
    if(!is_numeric($start)) return false; //检验参数
    if(!is_numeric($end)){ //与当前时间比较,交换参数间位置
        $max = $format==false ? 'default' : $format;
        $format=$end;
        $end = time();
    }
    $end===false AND $end = time();
    if($start>$end){ //比较大小,换位
        $start = $start^$end;
        $end = $start^$end;
        $start = $start^$end;
    }
    $diff = $end-$start;
    if($max=='s' || $format === false)return $diff;
    $map1 = array( //以秒为单位
        's'=>array(1,'sub'=>null,'format'=>null),
        'i'=>array(60,'sub'=>'s','format'=>'%I:%S'),
        'h'=>array(3600,'sub'=>'i','format'=>'%H:%I:%S'),
        'd'=>array(86400,'sub'=>'h','format'=>'%d %H:%I:%S')
    );
    $map2 = array( //以天为单位
        'w'=>array('sub'=>'d','format'=>' %H:%I:%S'),
        'm'=>array('sub'=>'d','format'=>'%m-%d %H:%I:%S'),
        'y'=>array('sub'=>'m','format'=>'%y-%m-%d %H:%I:%S')
    );
    $start = new \DateTime(date('Y-m-d H:i:s',$start));
    $end = new \DateTime(date('Y-m-d H:i:s',$end));
    $d_diff = $start->diff($end);
    if(array_key_exists($max,$map2)){
        $format OR $format=$map2[$max]['format']; //没有定义格式使用,默认格式
        if($max=='w'){ //最大单位周
            $format = str_ireplace('%w',floor($d_diff->days/7),$format);
            $format = str_ireplace('%d',$d_diff->days%7,$format);
        }elseif($max=='m'){ //最大单位月
            $d_diff->y>0 AND $d_diff->m +=$d_diff->y*12 ;
            $d_diff->y = 0;
        }
    }elseif($max!='default'){
        $format OR $format=$map1[$max]['format'];
        $result = floor($diff/$map1[$max][0]);
        $result = $result<10 ? '0'.$result : $result;
        $format = str_ireplace('%'.$max,$result,$format);
    }
    return $d_diff->format($format);
}

function daysInMonth($month, $year) { /* 获取某月的总天数 */
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

function isIp($ip){ /* 判断ip */
    return preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip);
}



function sizeTextFormat($size){ /* 计算文件大小 */
    $units = array('B', 'K', 'M', 'G', 'T', 'P');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) .  $units[$i];
}


function numFormat( $number, $decimals=0 ) { /* 保留几位小数(四舍五入) */
    return number_format($number, $decimals, '.', '');
}

function valueToStr(&$value){ /* 把数组的值全部转成字符串 */
    if(is_array($value)){
        foreach($value as &$row){
            if(is_array($row)){
                valueToStr($row);
            }else{
                $row = (string)$row;
            }
        }
    }else{
        $value = (string)$value;
    }
    return $value;
}






function numToLetter($num){ //数字转化成字母,只支持正整数
    $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if(floor($num/26)==0){
        return substr($str,$num%26,1);
    }else{
        $a = $num - floor($num/26)*26;
        return numToLetter(floor($num/26)-1).substr($str,$a,1);
    }
}




function getMicrotime(){ /* 获取毫秒数 */
    return str_replace('.','',numFormat(time()+microtime(),3));
}



function lastDayInMonth($time=false){ //获取某月的最后一天
    $time = $time===false ? NOW_TIME : $time;
    $BeginDate = date('Y-m-01', strtotime(date("Y-m-d",$time)));
    return date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
}

/**
 * 结果输出
 * 参数:result 结果数据
 * 返回:json格式化
 */
function outPut($result=array()){
    header("Content-Type:text/html;charset=utf-8");
    $code = $result['code'];
    $code==-3 AND $msg = D()->getLastSql();
    $msg = $msg ? $msg : $result['msg'];
    $result = json_encode($result);
    $exit = false;
    if($_GET['callback']){ //jsonp格式
        $result = $_GET['callback'].'('.$result.');';
        $exit = true;
    }elseif($_GET['define']){ //cmd格式
        if($_GET['define']=='TS'){
            $result = 'TS.define(function(){ return '.$result.'})';
        }else{
            $result = 'define(function(){ return '.$result.'})';
        }
        $exit = true;
    }elseif($_GET['script']){  //javascript格式
        $script = explode('.',$_GET['script']);
        if(count($script)==1){
            $result = 'var '.$script[0].' = '.$result.';';
        }else{
            $result = 'var '.$script[0].' = {};'.$script[0].'.'.$script[1].' = '.$result.';';
        }
        $exit = true;
    }elseif($_GET['ajax'] || $GLOBALS['ajax'] || in_array($_SERVER['HTTP_HOST'],C('DATA_HTTP'))){ //ajax请求
        $exit = true;
    }
    D('Common/Log','Logic')->addLog($result,$code,$msg); //后台操作日志记录
    $exit AND exit($result);
    return $result;
}

/**
 * 统计一个数转成二进制有几个1
 * 参数:$num 数字
 * 返回:数字;
 */
function countOne($num){
    $res = 0;
    while($num){
        $num &= ($num-1);
        $res++;
    }
    return $res;
}

function getCheckboxNum($num){
    $res = array();
    $i = 0;
    while($num){
        $num&1 AND $res[] = pow(2,$i);
        $num >>= 1;
        $i++;
    }
    return $res;
}

/**
 * 将数组1,2,4组成一个二进制数
 * 参数:$num 数字
 * 返回:数字;
 */
function checkboxNum($arr){
    if(!$arr){return 0;}
    $res = 0;
    foreach($arr as $value){
        $res |= $value;
    }
    return $res;
}

/**
 * 远程获取数据
 * 参数:$url 请求地址 ,$param 参数,$type 请求类型,$timeout 超时时间
 * 返回:array();
 */
function getData($url,$param=null,$type='get',$timeout=15){
    foreach($param as $key => &$value){
        $value = $key."=".$value;
    }
    $ch = curl_init($url) ;
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
    if($type=='get'){
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
    }else{
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,implode('&',$param));
    }
    $data = json_decode(curl_exec($ch),true);
    curl_close($ch);
    return $data;
}




