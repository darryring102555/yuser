/**
 * Created by zhang on 15-6-13.
 */
/* 先加载样式 */
define(['jquery','validate_cn','layer','common','icheck'],function(j,v,l,f){

    var  bind = {};
    bind.icheck = function(){
        j('.ts-icheck[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        j('.ts-icheck[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    }

    bind.verify = function () { /* 表单验证 */
        j(".login-form").validate({
            rules: {
                uname: {
                    required: true
                },
                password: {
                    required: true,
                    rangelength: [6, 18]
                }, verify: {
                    required: true,
                    rangelength: [4,4]
                }
            },
            messages: {
                uname: {"required": "请输入用户名"},
                password: {
                    "required": "必须输入密码",
                    "rangelength": "输入密码不正确"
                    },
                verify: {
                    "required": "请输入验证码",
                    "rangelength": "验证码长度不符"}
            },
            errorPlacement: function(error, element) {
                layer.tips(error.html(), element.parent(),{tipsMore: true});
            }
        });
    }


    f.onclick('form [type="submit"]:button', function () { /* 提交数据 */
        var $form = this.parents('form');
        if ($form.valid()){
            this.ajax($form.attr('action'), function (data) {
                if(data.code===0){ //登录成功
                   window.location.href = U('Index/index');
                }else{
                    f.alt(data);
                }
            }, $form.serialize(), 'post');
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