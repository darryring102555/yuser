/**
 * Created by zhang on 15-6-13.
 */

define(['jquery','template','common','layer','validate','boot_time_cn'],function(j,artTemplate,f,layer,b){
    var  bind = {};
    f.dele('.table input','change', function () { /* 改变数量事件 */
        var $this = $(this);
        var $count = $this.parents('tr').find('.count');
        var $tolal = $this.parents('div').find('.total');
        var total = Math.round(Number($tolal.html()) * 100);
        var count = Number($count.html());
        var price = Number($this.parents('td').next().html()) * 100;
        $tolal.html((total - (Number($this.attr('data-dfv')) - Number($this.val())) * price) / 100);
        $this.attr('data-dfv', $this.val());
        $count.html((Number($this.val()) * price) / 100);
    });


    f.dele('.table button','click', function () { /* 删除列表事件 */
        var $this = $(this);
        var $count = $this.parents('tr').find('.count');
        var count = Number($count.html());
        $this.parents('tr').remove();
        $('.total').html(Math.round(Number($('.total').html()) * 100 - count * 100) / 100)
    });


    f.dele('table .form-control','blur',function () { /* 检测输入框数字事件 */
        if (!$(this).val()) {
            $(this).val('0');
        }
    });


    f.onclick('.btn-primary',function () { /* 绑定添加事件 */
        var row = {"price": 3.88, "quantity": 10, "title": 'iphone'};
        var html = artTemplate('list', row);
        $('.total').html(Math.round(Number($('.total').html()) * 100 + row.price * row.quantity * 100) / 100)
        $(html).appendTo('.table');
    });



    f.onclick('.btn-info',function () { /* 整体刷新 */
        var time = new Date();
        var $this = this;
        var index = layer.load(2, {time: 10*1000});
        f.ajax(f.U('index'),function(data){
            layer.close(index);
            eval('window.' + TS.config.data_var + ' = data');
            requirejs(['model','contoller'], function (model,contoller) {
                model.init(); contoller.init();
            });
        },$this,null,'post');
    },1);


    f.onclick('.btn-warning', function () { /* 防止重复点击 */
        var time = new Date();
        var $this = this;
        f.ajax('http://data.test.com/index.php/Admin/Index/index.html?time=' + time.getTime(), function (data) {
            dumps(data);
        }, $this, null, 'post');
    }, 1);


    f.onclick('.dialog',function(){ /* 弹窗 */
        f.dialog({title:'可以弹窗了',content:'<h1>弹窗成功:</h1>点击其它不消失',shade:1});
    });


    f.onclick('.dialog1',function(){ /* 弹窗 */
        f.dialog({title:'可以弹窗了',content:'<h1>弹窗成功:</h1>点击其它消失'});
    });


    f.onclick('.alt',function(){ /* 提示 */
        f.alt({status:'danger',statusTitle:'警告',content:'不准点'});
    })


    f.onclick('.alt1',function(){ /* 提示 */
        f.alt({status:'info',statusTitle:'警告',content:'不准点',close:1});
    })


   $('.layer1').click(function(){
       layer.tips('默认就是向右的', '.layer2');
   });


    f.onclick('.layer2',function(){
        layer.tips('左边么么哒', '.layer2', {
            tips: [4, '#78BA32']
        });
    });

    bind.time = function(){
        j(".ts_time").datetimepicker({
            minView:2
        });
    }




    function init(){
        for(var i in bind){
            bind[i]();
        }
    }
    return {
        init:init
    };
});