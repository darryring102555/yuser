/**
 * Created by zhang on 15-6-13.
 */
define([],function(){
    var handleData = {
        /* 总价求和 */
        total : function(data){
            var count = 0;
            for(var i in data.data){
                count +=(data.data[i]['price']*100*data.data[i]['quantity']);
            }   

            data.total = count/100;
            return data;
        },   /* 总价求和 */
        ss : function(data){
            data.totals = 89999999;
            return data;
        }
    };

    /* 模板中需要用的函数 */
   var M = {

    }





    var glob = {
        M : M,
        disposeData : disposeData
    }

    /* 处理数据 */
    function disposeData(data){
        for(var i in handleData){
            data = handleData[i](data);
        }
        return data;
    }
    return glob;
});




