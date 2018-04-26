define(['jquery','common'],function(j,f){

    var  bind = {};
    
    //条件选中
    bind.whereCheckd = f.whereCheckd;

    //用户禁用启用切换
    f.onclick('.ts-forbid',function(){
        var $this = this;
        $this.ajax(U('swichForbid'),function(data){
            if(data.code===0){
                if($this.html()=='正常'){
                    $this.html('禁用');
                    $this.attr('data-status',1).removeClass('label-primary').addClass('label-warning');
                }else{
                    $this.html('正常');
                    $this.attr('data-status',2).removeClass('label-warning').addClass('label-primary');
                }
            }
            f.alt(data);
        },{"id":$this.attr('data-id'),"status":$this.attr('data-status')},'post');
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