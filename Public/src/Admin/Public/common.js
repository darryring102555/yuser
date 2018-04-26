/**
 * Created by zhangshiping on 15-6-7.
 */
define(['jquery','template','layer','app'],function(j,artTemplate,layer){


    function dele(expr,type,callback){ /* 绑定事件不因节点消失而消失 */
       j(document).on(type,expr,callback);
    }


    function click(expr,callback){ /* 防止重复点击事件 */
        j(expr).click(function(){
            var $this = j(this);
            $this.callback = callback;
            $this.ajax = ajax;
            if($this.data('click')){
               return false;
            }
            $this.data('click',1);
            $this.callback();
        });
    }


    function onclick(expr,callback){ /* 不消失防止重复点击 */
        dele(expr,'click',function(){
            var $this = j(this);
            $this.callback = callback;
            $this.ajax = ajax;
            if($this.data('click')){ //点击无效操作
                return false;
            }
            $this.data('click',1);
            if($this.callback()===false || $this.data('click')){
                return false;
            };
        });
    }



    function ajax(url,callback,data,type){ /* 防止重复请求,与onclick组合使用 */
        type = type ? type : 'get';
        data = data ? data : {};
        if(url.indexOf('?')==-1){url += '?'}
        url += '&ajax=1';
        var $this = this;
        j.ajax({
            type: type,
            url: url,
            data: data,
            dataType: 'json',
            success:function(){
                callback.apply(this,arguments);
                $this.data('click',0);
            },
            error:function(){
                alt({"code":3,"msg":'网络错误,请稍后再试!'});
                $this.data('click',0);
            }
        });
    }


    function strUper(str){ /* 把有下划线的单词大写 */
        str = str.toLowerCase();
        var strs=str.split("_");
        var result = '';
        for(var i in strs){
            result += strs[i].replace(/\b(\w)|\s(\w)/g, function(m){
                return m.toUpperCase();
            });
        }
        return result;
    }

    function dialog(obj){ /* 弹窗方法 */
        var html = artTemplate('dialog', obj);
        j(html).appendTo('body');
    }

    /**
     * 弹出提示消息
     * 参数:code:状态码 0-成功 3-警告  其它-提示
     *     msg:提示信息
     */
    function alt(obj){
        var html = artTemplate('alt', obj);
        var alt = j(html).appendTo('body').fadeIn("slow");
        if(obj.close!==false && !obj.close){ //全等于false不关闭
            if(obj.code==3||obj.code==1){ //警告信息10秒后关闭
                obj.close = 10;
            }else{ //提示,成功信息3秒后关闭
                obj.close = 3;
            }
        }
        if(obj.close){
            window.setTimeout(function(){
                alt.fadeOut("slow",function(){
                    alt.remove();
                })
            },obj.close*1000);
        }
    }

    /**
     * 赋值选中
     */
    function checkd(data){
        var data = data ? data : window[TS.config.data_var]['data'];
        for(var i in data){
            var obj = j('[name="'+i+'"]');
            if(obj.length){
                obj.val([data[i]]);
            }else if(typeof data[i]=='object'){
                obj = j('[name="'+i+'\\[\\]"]');
                if(obj.attr('type')=='hidden'){ //多图上传处理
                    for(var k in data[i]){
                       if(data[i][k]){
                           var obj1 = obj.last().clone();
                           obj1.val(data[i][k]);
                           obj.last().before(obj1);
                       }
                    }
                }else{
                    obj.val(data[i]);
                }

            }
        }
    }

    /**
     * 筛选条件赋值选中
     */
    function whereCheckd(){
        var where = window[TS.config.data_var]['where'];
        for(var val in where){
            if(typeof (where[val][1])=="object"){ //自定义条件选中
                if(j('[name="where\\['+val+'\\]\\[1\\]\\[0\\]"]').length){
                    for(var i in where[val][1]){
                        j('[name="where\\['+val+'\\]\\[1\\]\\['+i+'\\]"]').val(where[val][1][i]);
                    }
                    continue;
                }
            }else{
                if(j('[name="where\\['+val+'\\]\\[1\\]"]:not(.ts-search-value)').length){
                    j('[name="where\\['+val+'\\]\\[1\\]"]').val(where[val][1]);
                    continue;
                }
            }

            //多功能塞选条件选中
            j('.ts-search-field').val(val); //字段赋值
            j('.ts-search-type').attr('name','where['+val+'][0]');
            j('.ts-search-value').attr('name','where['+val+'][1]');
            j('[name="where\\['+val+'\\]\\[0\\]"]').val(where[val][0]);//条件类型赋值
            j('[name="where\\['+val+'\\]\\[1\\]"]').val(where[val][1]);//值赋值
        }
    }

    /**
     * 页面重新渲染
     * 参数:data-获取的数据对象
     *     callback 页面渲染完成的回调函数
     */
    function show(data,callback){
        window[TS.config.data_var] = data;
        requirejs(['model','contoller'], function (model,contoller) {
            model.init(); //页面渲染
            if("function"==typeof callback){
                callback();
            }
            contoller.init(); //重新绑定新加的节点事件
        });
    }

    /**
     * 页面ajax刷新操作
     * 参数:$body 触发事件对象(默认是body)
     *     noalert 不弹刷新页面提示消息
     */
    function refresh($body,noalert){
        if(!$body){
            var $body = j('body');
            if($body.data('click')){
                return false;
            }
        }
        $body.ajax = ajax;
        var data = {
            "where":window[TS.config.data_var]['where'], //查询条件
            "order":window[TS.config.data_var]['order'], //排序
            "p":window[TS.config.data_var]['page'] //页码
        };
        $body.ajax(U(''),function(data){
            show(data,function(){ //数据显示
                noalert || alt({"code":0,'msg':'页面刷新成功!'});
            });
        },data,'get');
    }

    /**
     * 页面ajax刷新操作事件绑定
     */
    onclick('.ts-refresh',function(){
        refresh(this);
    })

    /**
     * 弹窗关闭
     */
    onclick('.close-self',function(){
        j(this).parents('.self').remove();
    })

    /**
     * 点击其它消失弹窗
     */
    j(document).mouseup(function(e){
        var _con = j('.modal-dialog');
        if(!_con.is(e.target) && _con.has(e.target).length === 0){
            j('.dialog-close').remove();
        }
    });

    /**
     * 翻页功能
     */
    onclick('.pagination a',function(){
        var $li = this.parents('li');
        var page = 1;
        if($li.hasClass('disabled') || $li.hasClass('active')){
            return false;
        }
        if(this.attr('aria-label')=='Previous'){
            page = j('.pagination .active a').html()-1;
        }else if(this.attr('aria-label')=='Next'){
            page = Number(j('.pagination .active a').html())+1;
        }else{
            page = this.html();
        }
        window[TS.config.data_var]['page']=page; //设置页码
        refresh(this,1);
    });


    dele('.select-all','click',function(){ /* 全选 */
        var $this = this;
        j('input[name="id\[\]"][type="checkbox"]').each(function(){
            this.checked=$this.checked;
        })
    })


    onclick('.ts-remove',function(){ /* 删除单条数据 */
        var $this = this;
        layer.confirm('你确定删除该条数据吗？', {icon: 3}, function(index){
            layer.close(index);
            $this.ajax(U('remove'),function(data){
                if(data.code===0){
                    $this.parents('tr').remove();
                }
                alt(data);
            },{"id":$this.attr('id')},'post');
        });
        $this.data('click',0);
    });

    onclick('.ts-remove-refresh',function(){ /* 删除单条数据并刷新页面事件 */
        var $this = this;
        layer.confirm('你确定删除该条数据吗？', {icon: 3}, function(index){
            layer.close(index);
            $this.ajax(U('remove'),function(data){
                if(data.code===0){
                    refresh($this,1);//刷新页面,不弹窗
                }
                alt(data);
            },{"id":$this.attr('id')},'post');
        });
        $this.data('click',0);
    });


    onclick('.ts-remove-select',function(){ /* 删除选中 */
        var $this = this;
        var data = j('.data-list').serialize();
        if(data){
            layer.confirm('你确定所有选中数据吗？', {icon: 3}, function(index){
                layer.close(index);
                $this.ajax(U('remove'),function(data){
                    if(data.code==0){
                        j('.data-list input[name="id\[\]"][type="checkbox"]:checked').parents('tr').remove();
                    }
                    alt(data);
                },data,'post');
            });
        }
        $this.data('click',0);
    });

    onclick('.ts-remove-select-refresh',function(){ /* 删除选中并刷新页面 */
        var $this = this;
        var data = j('.data-list').serialize();
        if(data){
            layer.confirm('你确定所有选中数据吗？', {icon: 3}, function(index){
                layer.close(index);
                $this.ajax(U('remove'),function(data){
                    if(data.code==0){
                        refresh($this,1);//刷新页面,不弹窗
                    }
                    alt(data);
                },data,'post');
            });
        }
        $this.data('click',0);
    });

    /**
     * 筛选条件字段选择
     */
    dele('.ts-search-field','change',function(){
        var field = j(this).val(); //字段
        var type = j('.ts-search-type').val(); //条件类型
        j('.ts-search-type').attr('name','where['+field+'][0]');
        j('.ts-search-value').attr('name','where['+field+'][1]');
    })


    /**
     * 搜索不能清除排序
     */
    onclick('.ts-search',function(){
        var $form = this.parents('form');
        var where = $form.serialize();
        var order = window[TS.config.data_var]['order'];
        this.ajax(U('',where),function(data){
            show(data);
        },{'order':order},'get');
    });

    /**
     * 清空筛选条件
     */
    onclick('.ts-search-clear',function(){
        this.ajax(U(''),function(data){
            show(data);
        },{},'get');
    });


    /**
     * 字段排序
     */
    onclick('.ts-order',function(){
        var order = {};
        order[this.attr('data-filed')] = this.attr('data-order');
        delete window[TS.config.data_var]['order'][this.attr('data-filed')];
        window[TS.config.data_var]['order'] = j.extend(order,window[TS.config.data_var]['order']); //将刚刚点击的排序放前面
        refresh(this,1);
    });

    /* 验证码换图 */
    dele('.reloadverify','click',function(){
        var data = new Date();
        var $this = j('.verifyimg');
        $this.attr('src',U('Home/Index/verifyCode')+'?time='+(data.getTime()/1000));
    })

    /* 菜单事件 */
    j('.menu > li').click(function(){
        var $this = j(this);
        $this.find('ul').toggle('fast');
    });

    /* 鼠标移入移出用户信息 */
    j('.userinfo_box').hover(function(){
        var $this = j(this);
        $this.find('.userinfo_menu').slideToggle('fast');
        var $hello = $this.find('.hello');
        if($hello.html()=='↓'){
            $hello.html('↑');
        }else{
            $hello.html('↓');
        }
    });

    //退出后台登录
    onclick('.ts-admin-logout',function(){
        this.ajax(this.attr('href'),function(data){
            if(data.code===0){
               window.location.href = '/Admin/Login/index.html';
            }else{
                dumps(data);
                alt(data);
                return false;
            }
        });
    });












    return {
        dele : dele,
        click : click,
        onclick : onclick,
        ajax : ajax,
        dialog : dialog,
        alt : alt,
        checkd:checkd,
        whereCheckd:whereCheckd,
        refresh:refresh
    };
});
