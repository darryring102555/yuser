define(['jquery','common','validate_cn'
                                    ],function(j,f,v
                                    ){

    var  bind = {};
    
        /* 数据赋值 */
    bind.checkd = f.checkd;


    /* 表单验证 */
    bind.verify = function(){
        j("#edit").validate({
            rules: {
                uid:{required:true,number:true},
                "role_ids[]":{required:true,number:true}
            },messages: {
                uid:{"required":"必须填写用户id!","number":"用户id必须是大于0的整数!"},
                "role_ids[]":{required:'用户角色必选一个!',number:'角色id必须是大于0的整数!'}
            }, errorPlacement: function(error, element) {
                var $dl = element.parents('dl');
                $dl.find('.field_info').hide();
                $dl.find('.error_message').append(error);
            }});
    }

    /* 修改数据提交 */
    f.onclick('#edit [type="submit"]:button',function(){
        var $form = j("#edit");
        if($form.valid()){
            this.ajax($form.attr('action'),function(data){
                if(data.code===0){
                    if(data.id){ //新增数据
                        $form.find('[type="reset"]:button').trigger('click');
                    }else{ //修改数据
                        window.setTimeout(function(){
                            window.location.href= U('index');
                        },2000);
                    }
                }
                f.alt(data);
            },$form.serialize(),'post');
        }else{
            this.data('click',0);
        }
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