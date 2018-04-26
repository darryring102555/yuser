/**
 * Created by zhang on 15-6-13.
 */
/* 先加载样式 */
TS.addLink(['bootstrap']);
define(['jquery','template','common'],function(j,artTemplate,f){
    var  bind = {};
    f.onclick('form .create',function(){
        var $form = this.parents('form');
        this.ajax($form.attr('action'),function(data) {
            f.alt(data);
        },$form.serialize(), 'post');
    });




    function init(){
        for(var i in bind){
            bind[i]();
        }
    }
    return {
        init:init
    };
});