<?php
namespace Common\Controller;
use Think\Controller;
abstract class CommonController extends Controller {
    protected $dataHandleFront = ''; //前端是否还需要对数据进行处理
    protected $allTitle = '我的网站'; //页面主title
    protected $title = ''; //页面title,用于拼接
    protected $page = 1; //页码
    protected $stitacEdition = 20150616011; //全站静态文件版本控制
    protected $mustVerify = true; //验证登录标记等
    protected $userInfo = false; //用户信息
    protected $data = array(); //数据结果
    protected $request = array(); //过滤后请求参数
    protected $get = array();
    protected $post = array();
    protected $css = array();
    protected $header = true;
    protected $status = array( //全局返回状态码信息
        'w'=>array('code'=>CODE_W,'msg'=>''),//警告提示
        'i'=>array('code'=>CODE_I,'msg'=>''),//提示信息
        'p'=>array('code'=>CODE_P,'msg'=>'参数错误'),
        'ok'=>array('code'=>CODE_OK,'msg'=>'成功'),
        'f'=>array('code'=>CODE_F,'msg'=>'失败'),
        'no'=>array('code'=>CODE_NO,'msg'=>'没有数据'),
        'un'=>array('code'=>CODE_SE,'msg'=>'未知错误,请稍后再试'),
    );

    /* 初始化处理参数,页码等 */
    public function __construct(){
        parent::__construct();
        $this->handleParams();
        $this->request = array_merge($this->get,$this->post);
        if($this->mustVerify){ //登录验证
            $this->verify();
            $this->userInfo = session(C('DATA_KEY.S:User'));
        }
        $this->page = $_REQUEST['p'] > 1 ? intval($_REQUEST['p']) : 1;
        $this->setTitle($this->title);
    }

    /* 参数处理 */
    protected function handleParams(){
        $this->get = I('get.');
        $this->post = I('post.');
    }


    /* 错误访问页面 */
    public function _empty(){
        $this->outPut('i','你访问的请求地址不存在!');
    }

    /* 权限验证 */
    protected function verify(){
    }

    /* 分配数据 */
    protected function assigns($str,$data){
        $this->data[$str] = $data;
    }

    /* 防止非本站域名操作 */
    protected function setAjax(){
        if(isset($_GET["callback"]) && !preg_match("/^[\w_]+$/", $_GET["callback"])){
            $this->outPut('p','callback参数不合法!');
        }
        $res = explode('/',preg_replace('/^http:\/\/[\w]{0,}[\.]([A-Za-z0-9_]{1,})/','$1',$_SERVER['HTTP_REFERER']));
        $GLOBALS['ajax'] = 1;
        if(!(in_array($res[0],C('ALLOW_HTTP')) || (IS_AJAX && IS_POST) || CONTROLLER_NAME=='Upload')){
            $this->outPut('i','无权访问!');
        }
    }

    /* 设置前端是否还需要数据处理 */
    protected function setDataHande(){
        $this->dataHandleFront = '1';
    }

    /* 设置跨域ajax请求 */
    protected function setCross($domain='*'){
        header('Access-Control-Allow-Origin:'.$domain);
    }

    /* 设置title */
    protected function setTitle($title){
        if(!$title) return false;
        $this->allTitle =$title.'-'.$this->allTitle;
        $this->data['title'] = $this->allTitle;
    }

    /* 结果输出 */
    protected function outPut($data = array(),$msg=''){
        if(!is_array($data)){
            $msg AND $this->status[$data]['msg'] = $msg;
            $result = $this->status[$data];
        }else{
            $msg AND $this->status['ok']['msg'] = $msg;
            $data = array_merge($this->status['ok'],$data);
            $result = array_merge($this->data,$data);
            if(isset($data['list'])){ //分页数据计算
                $result['pageCount'] = (isset($data['count']) && $data['count']>0)? ceil($data['count']/C('PAGE_SIZE')) : 1;
                $result['page'] = $this->page > $result['pageCount'] ? $result['pageCount'] : $this->page; //当前页码
            }
        }
        $result = outPut($result);
        $this->shows($result);
    }


    /* 前端页面显示 */
    protected function shows($data){
        $this->header AND $this->setHeader();
        $css = '';
        foreach($this->css as $v){
            $css .= '<link href="'.$v.'.css" rel="stylesheet">'."\r\n";
        }
        exit( '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>' . $this->allTitle . '</title>
    <link href="'. C('TMPL_PARSE_STRING.__APP__').'reset.css" rel="stylesheet">
    '.$css.'
</head>
<body>
'.$this->header.'
    <div id="coutent"></div>
<script>
    var data = '. $data .';
</script>' . '
<script src="' . C('TMPL_PARSE_STRING.__TOOLJS__'). 'requirejs/2.1.18/require.min.js"
        data-main="' . C('TMPL_PARSE_STRING.__APP__').'main.js?time=' . $this->stitacEdition . '"
        module-name="' . MODULE_NAME . '"
        controller-name="' . CONTROLLER_NAME . '"
        action-name="' . ACTION_NAME . '"
        edition="' . $this->stitacEdition . '"
        var-model = "'.C('VAR_MODULE').'"
        var-controller = "'.C('VAR_CONTROLLER').'"
        var-action = "'.C('VAR_ACTION').'"
        id="entrance"
        debug="'.APP_DEBUG.'"
        hand-data="'.$this->dataHandleFront.'"
        data-name="data"
        target="coutent"
        defer async="true">
</script>
</body>
</html>');
    }

    /* 导航条HTML拼装 */
    protected function setHeader(){

    }



}

