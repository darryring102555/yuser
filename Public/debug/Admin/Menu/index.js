define(['jquery','common'],function(j,f){

    var  bind = {};
    
    //条件选中
    bind.whereCheckd = f.whereCheckd;

    // 移动节点位置
    f.onclick('.ts-moveUp', function () {
        var $this = this;
        var data = {
            'id':$this.parents('tr').find('input[name=id\\[\\]]').val(),
            'pid':$this.attr('data-pid')
        }
        layer.confirm('你确定要置顶么？', {icon: 3}, function(index){
            layer.close(index);
            $this.ajax(U('moveTop'), function (data){
                if(data.code===0){
                    f.refresh();
                }
                f.alt(data);
            },data, 'post');
        });
        this.data('click',1);
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