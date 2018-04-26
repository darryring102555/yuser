/**
 * Created by zhangshiping on 15-11-13.
 */


/* 返回值层级拼接 */
function deep(num) {
    var str = '|';
    for (var i = 1; i < num; ++i) {
        str += '—';
    }
    if (num > 1) {
        return str+':';
    }
    return '';
}


/* 树状数据变成一维数组 */
function treeToArr(tree, key, deep) {
    key = key ? key + '-' : '';
    deep = deep ? deep + 1 : 1;
    var result = [];
    if (typeof tree == 'object') {
        for (var i in tree) {
            if (typeof tree[i] != 'object') {
                result[result.length] = {"key": key + i, "k": i, "type": typeof tree[i], "value": tree[i], "deep": deep};
            } else {
                result[result.length] = {"key": key + i, "k": i, "type": typeof tree[i], "value": "array", "deep": deep};
                var res = treeToArr(tree[i], key + i, deep);
                for (var j = 0; j < res.length; j++) {
                    result[result.length] = res[j];
                }
            }
        }
    } else {
        result[result.length] = {"key": key, "k": 0, "type": typeof tree, "value": tree, "deep": deep};
    }
    return result;
}


/* url参数信息 */
function parseURL(url) {
    var a = document.createElement('a');
    a.href = url;
    return {
        source: url,
        protocol: a.protocol.replace(':', ''),
        host: a.hostname,
        port: a.port,
        query: a.search,
        params: (function () {
            var ret = {},
                seg = a.search.replace(/^\?/, '').split('&'),
                len = seg.length, i = 0, s;
            for (; i < len; i++) {
                if (!seg[i]) {
                    continue;
                }
                s = seg[i].split('=');
                ret[s[0]] = s[1];
            }
            return ret;
        })(),
        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ''])[1],
        hash: a.hash.replace('#', ''),
        path: a.pathname.replace(/^([^\/])/, '/$1'),
        relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ''])[1],
        segments: a.pathname.replace(/^\//, '').split('/')
    };
}


/* 调试打印 */
function dumps() {
    for (var i = 0; i < arguments.length; ++i) {
        console.log(arguments[i]);
    }
}

$(function(j){
    function handParams() {
        var $form = $('#api');
        $form.attr('method', $('[name="tool_m_type"]:input:checked').val());
        //获取参数
        var $p_name = $('[name="p_name[]"]:not(:hidden)'); //参数名
        var params = '';
        if($('[name="tool_json"]:input:checked').val()!=1){ //如果参数不转json
            $('[name="p_value[]"]:not(:hidden)').each(function (a, b) {
                var key = $.trim($($p_name[a]).val());
                var val = $(b).val();
                if (key && $.trim(val)) {
                    params +='<input type="hidden" name="'+key +'" value="'+val+'">';
                };
            });
            $('#api').html(params);
            return true;
        }

        //参数json化
        $('[name="p_value[]"]:not(:hidden)').each(function (a, b) {
            var key = $.trim($($p_name[a]).val());
            var val = $(b).val();
            if (key && $.trim(val)) {
                if ((val.indexOf('}') != -1) || (val.indexOf(']') != -1)) {
                    params += '"' + key + '":' + val + ',';
                } else {
                    params += '"' + key + '":"' + val + '",';
                }
            }
        });
        if(params.substr(params.length-1,1)==','){
            params = params.substring(0,params.length-1);
        }
        if (params.indexOf(',') == 0) {
            params = params.substr(1, params.length);
        }

        params = '{' + params + '}';
        $('form input[name="params"]').val(params);
    }

    //请求路径处理
    if (parseURL(window.location.href)['port'] != '')
        $('#api').attr('action', 'http://'
            + parseURL(window.location.href)['host']
            + ':'
            + parseURL(window.location.href)['port']
            + $('#api').attr('action'));
    else
        $('#api').attr('action', 'http://'
            + parseURL(window.location.href)['host']
            + $('#api').attr('action'));


    var html = template('table', {data: interfaces, select: [0, 0]});
    window.document.getElementById('content').innerHTML = html;
    template.helper('deep', deep); //模板绑定访问外部变量方法

    /* 添加参数 */
    $('.add').click(function () {
        var html = template('tr', {});
        $('table').append(html);
    });

    /* 置空 */
    $('.reset').click(function () {
        $('#tb').find('[name="p_value[]"]').val(['']);
    });

    /* 提交 */
    $('.submit').click(function () {
        handParams();
        var $form  = $('#api')
        var url = $.trim($('#interface-url').attr('data-url'));
        var y_url = $form.attr('action');
        url = y_url+url;
        var pas = parseURL(url)['params'];
        var html = '';
        for(var i in pas){
            html +='<input type="hidden" name="'+ $.trim(i)+'" value="'+pas[i]+'">';
        }
        $form.append(html);
        $form.attr('action',url);
        $form.attr('method','get').submit();
        $form.attr('action',y_url);
    });

    /* 提交数据获取返回数据结果 */
    $(document).on('click', '#look', function () {
        handParams();
        var $form = $('#api');
        var url = $.trim($('#interface-url').attr('data-url'));
        url = $form.attr('action')+url;
        var obj = {
            type:$form.attr('method'),
            url: url,
            data: $form.serialize(),
            success: function (data) {
                dumps(data);
                try{
                    eval('data = ' + data + ';');
                }catch (e){
                    if('object' != typeof data){
                        alert('返回数据结构不正确!');
                        return false;
                    }
                }

                data.data = treeToArr(data, '');
                data.ruesult_info = interfaces[$('[name="tool_classify"]').val()]['interface'][$('[name="tool_interface"]').val()]['result_info'];//当前分类
                var html = template('result-tab', data);
                window.document.getElementById('result').innerHTML = html;
            },
            error: function () {
                alert('网络错误!');
            }
        };
        if($('[name="tool_jsonp"]:input:checked').val()==1){
            obj.dataType = 'jsonp';
        }
        $.ajax(obj);
    })

    /* 切换分类 */
    $(document).on('change', '[name="tool_classify"]', function () {
        var html = template('table', {data: interfaces, select: [$(this).val(), 0]});
        window.document.getElementById('content').innerHTML = html;
    })

    /* 选择接口 */
    $(document).on('change', '[name="tool_interface"]', function () {
        var html = template('table', {data: interfaces, select: [$('[name="tool_classify"]').val(), $(this).val()]});
        window.document.getElementById('content').innerHTML = html;
        window.document.getElementById('result').innerHTML = '';
    })

});








