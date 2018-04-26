/**
 * Created by zhang on 15-6-13.
 */
define(TS.modelCore,function(template,fun,glob_tpl,hdata){
    if(!TS.config.debug){
      for(var i in glob_tpl.cache){
          template.cache[i] = glob_tpl.cache[i];
      }
    }
    function init(){
        template.helper('F', F); //模板绑定调用外部函数方法
        template.helper('C', C); //模板绑定调用外部函数方法
        template.helper('U', U); //模板绑定调用外部函数方法
        var data = window[TS.config.data_var];
        if(typeof data === 'undefined'){
            data = {};
        }
        data.$1 = '{{'; //用于模板中创建模板后续使用
        data.$2 = '}}';
        dumps(data);
        var html = template(TS.config.a,data);

        if(TS.config.target == 'body' || !TS.config.target || !window.document.getElementById(TS.config.target)){
            //当未设置目标位置或者设置错误时,页面直接渲染整个页面
            window.document.getElementsByTagName('body')[0].innerHTML = html;
        }else{
            window.document.getElementById(TS.config.target).innerHTML = html;
        }
    }
    init();
    return {
        init : init
    }

});




