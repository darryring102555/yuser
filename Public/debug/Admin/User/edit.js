define(['jquery', 'common', 'validate_cn', 'uploadify', 'ueditor_cn','icheck','ace_elements','colorpicker','select2'], function (j, f, v, u, UE,icheck) {

    var bind = {};


    bind.ueditor = function () {
        window.UEDITOR_CONFIG.textarea = j('#ts-editor').attr('data-name');
        var editor = UE.getEditor('ts-editor');
        editor.ready(function () {
            var value = window[TS.config.data_var]['data'][UEDITOR_CONFIG.textarea] || '';
            editor.setContent(value);
        });
    }
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
    bind.coler = function(){
        $(".ts-color").colorpicker();
        $(".ts-select").select2();
    }
    bind.ace = function(){
        j('.ts-number').each(function(){
            var $this = j(this);
            var obj = {};
            obj.value = $this.val() || 0;
            obj.min = $this.attr('min') || 0;
            obj.max = '00'+$this.attr('max') || 9999;
            obj.step = $this.attr('step') || 1;
            obj.icon_up = 'icon-plus smaller-75';
            obj.icon_down = 'icon-minus smaller-75';
            obj.btn_up_class = 'btn-success';
            obj.btn_down_class = 'btn-danger';
            obj.on_sides = true ;
            j(this).ace_spinner(obj);
        });
    }


    /* 表单验证 */
    bind.verify = function () {
        j("#edit").validate({
            rules: {
                uname: {required: true}, password: {rangelength: [6, 18], required: true}, name: {minlength: 3, noNumber: true}, email: {email: true, required: true}, phone: {required: true}, integral: {number: true}            },
            messages: {
                uname: {"required": "true"}, password: {"rangelength": "[6,18]", "required": "true"}, name: {"minlength": "3", "noNumber": "true"}, email: {"email": "true", "required": "true"}, phone: {"required": "true"}, integral: {"number": "true"}            },
            errorPlacement: function (error, element) {
                var $dl = element.parents('dl');
                $dl.find('.field_info').hide();
                $dl.find('.error_message').append(error);
            }
        });
    }

    /* 修改数据提交 */
    f.onclick('#edit [type="submit"]:button', function () {
        var $form = j("#edit");
        if ($form.valid()) {
            this.ajax($form.attr('action'), function (data) {
                if (data.code === 0) {
                    if (data.id) { //新增数据
                        $form.find('[type="reset"]:button').trigger('click');
                    } else { //修改数据
                        window.setTimeout(function () {
                            window.location.href = U('index');
                        }, 2000);
                    }
                }
                f.alt(data);
            }, $form.serialize(), 'post');
        } else {
            this.data('click', 0);
        }
    });

    //删除图片事件
    f.dele('.uploadify-button-text', 'click', function () {
        var $obj = j(this).parents('.uploadify');
        $obj.prev('input').remove();
        $obj.next().remove();
        $obj.remove();
    })
    /* 图片上传配置 */
    var obj = {
        'swf': '/Public/tooljs/uploadify/3.2.1/uploadify.swf', //上传图片是post数据,必须用同域名路径
        'uploader': '/index.php/Home/Upload/img',
        'height': 295,
        'width': 210,
        "buttonText": "",
        //'formData': {'dir': j('#ts-img').siblings('.upload-img-box').find('input').attr('name')},
        'fileTypeExts': '*.gif; *.jpg; *.png;*.bmp;*.jpeg',
        'debug': false,
        'onUploadSuccess': function (file, data) {
            eval('var data = ' + data);
            if (data.code === 0) {
                var $img = j(this.button).find('img');
                $img.attr('src', $img.attr('url') + data.pic + '?time=' + Math.random()); //新图上传成功显示
                var $dl = $img.parents('dl');
                var index = $dl.find('.ts-show-img').index($img);
                if ($img.parents('.uploadify').hasClass('ts-add-img')) { //新加上传图片组建
                    $img.prev('span').html('删除');
                    var $hidden = $dl.find('.ts-img,.ts-imgs:eq(' + index + ')').last().clone();
                    $img.parents('dd').append($hidden); //添加新隐藏域
                    var k = $hidden.attr('name').replace('[', '').replace(']', '');
                    var uid = window[TS.config.data_var]['data']['id'] || '';
                    obj.formData = {'dir': k,'uid':uid};
                    obj.buttonText = '';
                    obj.height = $hidden.attr('data-height');
                    obj.width = $hidden.attr('data-width');
                    var i = $img.parents('.uploadify').attr('data-len');
                    $hidden.after(j('<input type="file" id="img-' + k + '-' + i + '"/>'));
                    j('#' + 'img-' + k + '-' + i).uploadify(obj);
                    var $img1 = $hidden.siblings('img').clone().css({width: obj.width + 'px',
                        height: obj.height + 'px'}).addClass('ts-show-img');
                    $img1.appendTo(j('#' + 'img-' + k + '-' + i).find('.uploadify-button'));
                    j('#' + 'img-' + k + '-' + i).addClass('ts-add-img').attr('data-len', i + 1);
                    $img.parents('.uploadify').removeClass('ts-add-img');
                }
                ;
                $img.parents('dl').find('.ts-img,.ts-imgs:eq(' + index + ')').val(data.pic); //给隐藏域赋值

            }
        },
        'onFallback': function () {
            f.alt({'code': 3, 'msg': '未检测到兼容版本的Flash.'});
        }
    };    //上传组建初始化,单张图片处理
    bind.upload = function () {
        j('.ts-img').each(function () {
            var $this = j(this);
            var k = $this.attr('name');
            var uid = window[TS.config.data_var]['data']['id'] || '';
            obj.formData = {'dir': k,'uid':uid};
            obj.height = $this.attr('data-height');
            obj.width = $this.attr('data-width');
            $this.after(j('<input type="file" id="img-' + k + '"/>'));
            j('#' + 'img-' + k).uploadify(obj);
        });
    }    //上传组建初始化,多张图片处理
    bind.uploads = function () {
        j('.ts-imgs').each(function (i) {
            var $this = j(this);
            //图片数据传入的key
            var k = $this.attr('name').replace('[', '').replace(']', '');
            obj.buttonText = '删除';
            obj.formData = {'dir': k};
            obj.height = $this.attr('data-height');
            obj.width = $this.attr('data-width');
            //图片所在编号
            var $imgs = j(this).parents('dd').find('.ts-imgs');//该图片集所有图片
            var index = $imgs.index($this);
            //上传组件ID
            var str = 'img-' + k + '-' + index;
            if ((index + 1) == $imgs.length) { //删除图片功能
                obj.buttonText = '';
            }
            j(this).after(j('<input type="file" id="' + str + '"/>'));
            j('#' + str).uploadify(obj);
            //最后一个上传组建加上新建组建样式
            if ((index + 1) == $imgs.length) {
                j('#' + str).addClass('ts-add-img').attr('data-len', index + 1);
            }
        });
    }    //图片显示
    bind.imgs = function () {
        j('.ts-img,.ts-imgs').each(function (i, item) {
            var $this = j(this);
            var $img = $this.siblings('img').clone().css({width: $this.attr('data-width') + 'px',
                height: $this.attr('data-height') + 'px'}).addClass('ts-show-img');
            if (j.trim($this.val())) {
                $img.attr('src', $img.attr('url') + $this.val());
            }
            $img.appendTo($this.next().find('.uploadify-button'));
        });
    }

    function init() {
        for (var i in bind) {
            bind[i]();
        }
    }

    return {
        init: init
    };
});