define(['jquery','common','template'],function(j,f,t){

    var  bind = {};
    
    //条件选中
    bind.whereCheckd = f.whereCheckd;
    //成员查询
    f.onclick('.user-list', function () {
        this.ajax(U('userList'), function (data) {
            if (data.code === 0) {
                layer.open({
                    type: 1,
                    title :['成员列表','text-align: center;'],
                    closeBtn: 2,
                    skin: 'yourclass',
                    content: t('userList',data),
                    area: ['600px','50%'],
                    skin:'radis10'
                });
            }else{
                f.alt(data);
            }
        },{'id':this.attr('data-id')}, 'post');
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