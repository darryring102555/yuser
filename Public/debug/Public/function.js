/**
 * 全局函数定义
 * Created by zhangshiping on 15-6-7.
 */

/* 将具有层级的数据简单图形化显示 */
function deep(num){
    var str = '|';
    for(var i= 1;i<num;++i){
        str +='—';
    }
    if(num>1){
        return str;
    }
    return '';
}


/* 格式化时间方法 */
function dateFormat(date, format) {
    if(typeof(format)!='string'){
        var now = new Date();
        var timestamp = now - (date * 1000);
        if (timestamp < 60000) {
            var timeNum = Math.floor(timestamp / 1000);
            if (timeNum < 1) {
                return '刚刚';
            }
            return timeNum + '秒前';
        } else if (timestamp < 60000 * 60) {
            return Math.floor(timestamp / 60000) + '分钟前';
        } else if (timestamp < 60000 * 60 * 24) {
            return Math.floor(timestamp / (60000 * 60)) + '小时前';
        } else if (timestamp < 60000 * 60 * 24 * 30) {
            return Math.floor(timestamp / (60000 * 60 * 24)) + '天前';
        } else if (timestamp < 60000 * 60 * 24 * 30 * 12) {
            return Math.floor(timestamp / (60000 * 60 * 24 * 30)) + '月前';
        } else {
            return Math.floor(timestamp / (60000 * 60 * 24 * 30 * 12)) + '年前';
        }
    }
    date = new Date(date * 1000);
    var map = {
        "M": date.getMonth() + 1, //月份
        "d": date.getDate(), //日
        "h": date.getHours(), //小时
        "m": date.getMinutes(), //分
        "s": date.getSeconds(), //秒
        "q": Math.floor((date.getMonth() + 3) / 3), //季度
        "S": date.getMilliseconds() //毫秒
    };
    format = format.replace(/([yMdhmsqS])+/g, function (all, t) {
        var v = map[t];
        if (v !== undefined) {
            if (all.length > 1) {
                v = '0' + v;
                v = v.substr(v.length - 2);
            }
            return v;
        }else if (t === 'y') {
            return (date.getFullYear() + '').substr(4 - all.length);
        }
        return all;
    });
    return format;
}

function U(url, params,type) { /* 拼接数据地址 */
    if('undefined'!=typeof type){
        var website = TS.config['_DATA_URL_']+'index.php/';
    }else{
        var website = TS.config['path_info']['protocol']+'://'+TS.config['path_info']['host']+'/'+'index.php/';
    }
    if('undefined'==typeof url){
        url = '';
    }
    var url1 = url;
    if(!url1){
        website += eval('TS.config["'+TS.config['m_var']+'"]')+'/'+eval('TS.config["'+TS.config['c_var']+'"]')+'/'+eval('TS.config["'+TS.config['a_var']+'"]')+'.html';
    }else{
        var url = url.split('/');
        var len = url.length;
        if(len<=1){
            website += eval('TS.config["'+TS.config['m_var']+'"]')+'/'
                +eval('TS.config["'+TS.config['c_var']+'"]')+'/'+url[0]+'.html';
        }else if(len==2){
            website += eval('TS.config["'+TS.config['m_var']+'"]')+'/'+url[0]+'/'+url[1]+'.html';
        }else if(len>=3){
            website += url1+'.html';
        }
    }
    if('undefined'!=typeof params){
        website += '?'+params;
    }
    return website;
}


function C(str){
    var res = window['TS']['config'][str];
    return res ? res : '';
}

/* 模板中调用外部方法 */
function F(){
    var f = eval(arguments[0]);
    arguments.splice = [].splice;
    var p = arguments.splice(1);
    return f.apply(this,p);
}
