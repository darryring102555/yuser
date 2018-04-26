define(['jquery','common','validate_cn'
                    ,'icheck'                ],function(j,f,v
                    ,icheck                ){

    var  bind = {};
    
        /* 数据赋值 */
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

    //权限选择
    f.dele('.permission_list input:enabled','click',function(){
        var $this = j(this);
        var checked = this.checked;
        var rght = $this.attr('data-rght');
        var lft = $this.attr('data-lft');
        j('.permission_list input:enabled').each(function(){
            var $this = j(this);
            if((lft<Number($this.attr('data-lft')) && Number($this.attr('data-rght'))<rght)
                ||(checked && lft>Number($this.attr('data-lft')) && Number($this.attr('data-rght'))>rght)){
                this.checked=checked;
            }
        })
    });

    /* 表单验证 */
    bind.verify = function(){
        j("#edit").validate({
            rules: {
                name:{required:true}            },
            messages: {
                name:{"required":"true"}            },
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