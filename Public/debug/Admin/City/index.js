define(['jquery','common'],function(j,f){

    var  bind = {};
    
    //条件选中
    bind.whereCheckd = f.whereCheckd;


    function init(){
        for(var i in bind){
            bind[i]();
        }
    }
    return {
        init:init
    };
});