/**
 * Created by zhangshiping on 15-6-13.
 * 全站通用页面加载调用
 * 张世平
 */


(function (){

    /* 去掉前后空格 */
    String.prototype.trim=function() {
        return this.replace(/(^\s*)|(\s*$)/g,'');
    }
    var url = parseURL(window.location.href); //当前访问路径信息
    var HOST = url.host;
    HOST = HOST.substring(HOST.indexOf('.'),HOST.length); //域名

    var config = {
        debug : getAttr('debug',''),
        entrance :getAttr('edition',''), //js,css等版本号,用于主动更新浏览器的静态文件
        m_var : getAttr('var-model','m'), //框架使用的模块变量名
        c_var : getAttr('var-controller','c'), //框架使用的控制器变量名
        a_var : getAttr('var-action','a'), //框架使用的方法变量名
        data_var :getAttr('data-name','data'), //数据变量名
        hdata : getAttr('hand-data',''), //数据是否前端处理
        m : getAttr('module-name','Home'), //模块
        c : getAttr('controller-name','Index'), //控制器
        a : getAttr('action-name','index'), //方法名
        target : getAttr('target','body'), //渲染出的页面存放位置
        define : define, //先保存AMD的定义方法,方便兼容CMD模式
        path_info : url , //页面路径信息
        _DATA_URL_:getHost('www',''), //数据地址
        _STATIC_URL_:getHost('www','Public/'), //静态文件地址
        _CSS_ : getHost('www','Public/css/'), //样式地址
        _JS_ : getHost('www','Public/'), //样式地址
        _TOOLJS_ : getHost('www','Public/tooljs/'), //三方js库
        _IMG_ : getHost('www','Public/img/') //图片地址
    }
    if(typeof window['TS']!='object'){
        window['TS'] = {};
    }
    if(typeof window[config.data_var]!='object'){
        window[config.data_var] = {};
    }

    TS.config = config;
    TS.addLink = addLink;
    requirejs.config({paths:{"config" :'/Public/config'}}); //配置文件路径设置
    requirejs(['config'], function (src){
        //dumps(src);
        TS.link = src.css;
        requirejs.config(src);
        TS.core = ['jquery','contoller','model']; //核心加载
        TS.modelCore = [];
        TS.modelCore[0] = 'template';
        TS.modelCore[1] = 'fun';
        TS.modelCore[2] = '';
        if(TS.config.hdata){ //数据处理加载对应的model
            TS.modelCore[3] = 'hdata';
        }

        if(TS.config.debug){
            requirejs(['jquery'], function (j){
                /* 模板加载 */
                j.get(src.paths.debug_pubtpl,{},function(tpl){
                    j(tpl).appendTo('body'); //公用模板
                    /* 模板加载 */
                    j.get(src.paths.debug_template,{},function(html){
                        html = html.replace(/\{\{include '\.\//g,'{{include \'');
                        html = html.replace(/\{\{include "\.\//g,'{{include \"');
                        j('<script type="text/html" id="'+TS.config.a+'">'+html+'</script>').appendTo('body');
                        run();
                    },'html');
                },'html');
            });
        }else{
            TS.modelCore[2] = 'pubtpl';
            run();
        }
    });

    function run(){


        requirejs(TS.core, function (j,contoller,model){
            contoller.init(); //初始化页面事件
            if("undefined" !== typeof TS['youerDefine']){
                window.define = TS['youerDefine'];
                TS['youerDefine'] = undefined;
            }
        });
    }

    /* 获取路径参数 */
    function parseURL(url) {
        var a =  document.createElement('a');
        a.href = url;
        return {
            source: url,
            protocol: a.protocol.replace(':',''),
            host: a.hostname,
            port: a.port,
            query: a.search,
            params: (function(){
                var ret = {},
                    seg = a.search.replace(/^\?/,'').split('&'),
                    len = seg.length, i = 0, s;
                for (;i<len;i++) {
                    if (!seg[i]) { continue; }
                    s = seg[i].split('=');
                    ret[s[0]] = s[1];
                }
                return ret;
            })(),
            file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
            hash: a.hash.replace('#',''),
            path: a.pathname.replace(/^([^\/])/,'/$1'),
            relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
            segments: a.pathname.replace(/^\//,'').split('/')
        };
    }
    function getAttr(attr,def){
        return window.document.getElementById('entrance').getAttribute(attr).trim() || def;
    }
    function getHost(sld,path){ //二级域名返回
        return 'http://'+sld+HOST+'/'+path;
    }
    function addLink(links){ //样式加载
        if(typeof links=='undefined'){
            return false;
        }
        var str = '';
        for(var i=0;i<links.length;++i){
            if(TS.link[links[i]]){
                str += TS.link[links[i]];
                TS.link[links[i]] = '';//加载过的css,不重复加载
            }
        }
        if('undefined' !== typeof $){
            $(str).appendTo('head');
        }else if(navigator.userAgent.indexOf('MSIE') >=0 ){ //ie兼容
            requirejs(['jquery'],function($){
                $(str).appendTo('head');
            })
        }else{
            var head= window.document.getElementsByTagName('head')[0];
            head.innerHTML = head.innerHTML + str;
        }

    }

})();

/* 调试打印 */
function dumps(){
    for (var i = 0; i < arguments.length; ++i) {
        console.log(arguments[i]);
    }
}
