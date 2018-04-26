define(['jquery','common','validate_cn','uploadify'],function(j,f,v,u){

    var  bind = {};

    /* 表单验证 */
    bind.verify = function(){
        j("#edit").validate({
            rules: {
                old_password:{
                    required:true,
                    rangelength:[6,18],
                    noNumber:true
                },
                password:{
                    required:true,
                    rangelength:[6,18],
                    noNumber:true
                },
                repassword:{
                    required:true,
                    equalTo: "#password"
                }
            },
            messages: {
                old_password:{
                    required:'请输入原密码',
                    rangelength:'请输入6-16位字母加数字,不能是纯数字',
                    noNumber:'不能是纯数字'
                },
                password:{
                    required:'请输入新密码',
                    rangelength:'请输入6-16位字母加数字,不能是纯数字',
                    noNumber:'不能是纯数字'
                },
                repassword:{
                    required:'请确认新密码',
                    equalTo: "两次输入密码不一致"
                }
            },
            errorPlacement: function(error, element) {
                var $dl = element.parents('dl');
                $dl.find('.field_info').hide();
                $dl.find('.error_message').append(error);
            }
        });
    }

    /* 修改数据提交 */
    f.onclick('#edit [type="submit"]:button',function(){
        var $form = j("#edit");
        if($form.valid()){
            this.ajax($form.attr('action'),function(data){
                if(data.code===0){
                    j('[type="reset"]').trigger('click');
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