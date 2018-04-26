define(['jquery','common','validate_cn'
                ,'ueditor_cn'    ,'icheck'                ],function(j,f,v
                ,UE    ,icheck                ){

    var  bind = {};
    
    bind.ueditor = function(){
            window.UEDITOR_CONFIG.textarea = j('#ts-editor').attr('data-name');
            var editor = UE.getEditor('ts-editor');
            editor.ready(function(){
                var value = window[TS.config.data_var]['data'][UEDITOR_CONFIG.textarea] || '';
                editor.setContent(value);
            });
        }    /* 数据赋值 */
    bind.checkd = f.checkd;

    //单选框,复选框组件
    bind.icheck = function(){
        j('.ts-icheck[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        j('.ts-icheck[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    }                
    /* 表单验证 */
    bind.verify = function(){
        j("#edit").validate({
            rules: {
                table:{required:true,noNumber:true},alias:{required:true,noNumber:true},name:{required:true,noNumber:true},sort:{number:true}            },
            messages: {
                table:{"required":"true","noNumber":"true"},alias:{"required":"true","noNumber":"true"},name:{"required":"true","noNumber":"true"},sort:{"number":"true"}            },
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