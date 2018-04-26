/**
 * Created by zhang on 15-11-22.
 */
define([], function () {
    /* 配置变量获取 */
    var config = TS.config;
    var tool = config._TOOLJS_;
    var static = config._STATIC_URL_;
    var css = config._CSS_;

    TS.config.debug = true; //调试模式路径切换
    var path = TS.config.debug ? 'debug/' : 'src/';
    path  = config._JS_+path;

    /* 配置对象 */
    var obj = {
        "tool_path":{
            "underscore": 'underscore/1.8.3/underscore',
            "joint": 'joint/0.9.5/joint',
            "lodash": 'lodash/3.10.1/lodash',
            "backbone": 'backbone/1.2.3/backbone',
            "ztree": 'ztree/3.4/ztree',
            "uploadify": 'uploadify/3.2.1/uploadify',
            "seajs": 'seajs/3.0.1/sea',
            "less": 'less/2.5.3/less',
            "angular": 'angular/1.4.7/angular',
            "bootstrap": 'bootstrap/3.3.4/bootstrap',
            "boot_time": ['bootstrap/3.3.4/bootstrap-datetimepicker.min'],
            "boot_time_cn": ['bootstrap/3.3.4/bootstrap-datetimepicker-cn.min'],
            "colorpicker":['bootstrap/3.3.4/bootstrap-colorpicker.min'],
            "cookie": ['cookie/1.4.1/cookie.min'],
            "jquery": ["jquery/2.0.0/jquery.min"],
            "jquery180": ["jquery/1.8.0/jquery.min"],
            "icheck": ["icheck/icheck.min"],
            "layer": ["layer/layer.min"],
            "validate": ["validate/1.13.1/validate.min"],
            "validate_cn":["validate/1.13.1/validate_cn"],
            "artTemplate":  ["artTemplate/3.0.0/template.min"],
            "wdatePicker":  ["My97DatePicker/WdatePicker"],
            "ueditor_config": ["ueditor/ueditor.config"],
            "ueditor": ["ueditor/ueditor.all"],
            "ueditor_cn": ["ueditor/lang/zh-cn/zh-cn.min"],
            "app": ["ace/app.min"],
            "fuelux":['fuelux/fuelux.spinner.min'],
            "ace_elements": ["ace/ace-elements.min"],
            "ZeroClipboard" : ['ueditor/third-party/zeroclipboard/ZeroClipboard.min'],
            "select2":['select2/select2.full.min']

        },
        "css":{
            "joint" : 'joint/0.9.5/joint',
            'ztree' : 'ztree/3.4/ztree',
            'layer.ext' :'layer/skin/layer.ext',
            'layer' : 'layer/skin/layer',
            'uploadify' : ['uploadify/3.2.1/uploadify','uploadify/3.2.1/upload'],
            'bootstrap' : 'bootstrap/3.3.4/bootstrap.min',
            'boot_time' : 'bootstrap/3.3.4/bootstrap-datetimepicker.min',
            "colorpicker":'bootstrap/3.3.4/bootstrap-colorpicker.min',
            "ueditor": "ueditor/themes/default/css/ueditor",
            "icheck":'icheck/all',
            "select2":'select2/select2.min',
            "ace_elements":''
        },
        "paths": {
            "fun": path+'Public/function.js?time=' + config['entrance'],
            "common": path+config.m + '/Public/common.js?time=' + config['entrance'],
            "pubtpl": path+'Public/template.js?time=' + config['entrance'], //模板模板渲染
            "debug_pubtpl": path+'Public/public.html?time=' + config['entrance'], //模板模板渲染
            "debug_template": path + config.m + '/' + config.c + '/' + config.a +'.html?time=' + new Date().getTime(), //模板模板渲染
            "template": path + config.m + '/' + config.c + '/' + 'template.js?time=' + config['entrance'], //模板模板渲染
            "hdata": path + config.m + '/' + config.c + '/' + config.a + '-model.js?time=' + config['entrance'], //数据处理
            "model": static + 'main-model.js?time=' + config['entrance'], //模板渲染
            "contoller": path + config.m + '/' + config.c + '/' + config.a + '.js?time=' + config['entrance'] //事件绑定
        }
    }

    if(config.m=='Create'){
        obj.paths.common = path+'Admin/Public/common.js?time=' + config['entrance'];
    }


    /* 处理配置 */
    for(var i in obj['tool_path']){
        if(typeof obj['tool_path'][i]=='string'){
            obj['paths'][i] = jointToolJs(obj['tool_path'][i]);
        }else{
            obj['paths'][i] = jointToolJs(obj['tool_path'][i][0],1);
        }
    }
    for(var i in obj['css']){
        obj['css'][i] = jointCss(obj['css'][i]);
    }
    if(TS.config.debug){
        obj['paths']['template'] = obj['paths']['artTemplate'];
    }

    return obj;

    /* 拼接完整的css路径 */
    function jointCss(arr){
        if(typeof arr == 'string'){arr = [arr];}
        var result = '';
        for(var j=0;j<arr.length;++j){
            if(arr[j]){
                result += '<link rel="stylesheet" href="'+tool+arr[j]+'.css">';
            }
        }
        return result;
    }

    /* 拼接完整的js路径 */
    function jointToolJs(str,flog){
        var result = '';
        if(!str){return result;};
        var suffix = TS.config.debug ? '' : '.min';
        if(typeof flog !='undefined')suffix = '';
        return tool+str+suffix;
    }
});