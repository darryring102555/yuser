define(["jquery","common"],function(a,b){function d(){for(var a in c)c[a]()}var c={};return c.whereCheckd=b.whereCheckd,b.onclick(".ts-moveUp",function(){var a=this,c={id:a.parents("tr").find("input[name=id\\[\\]]").val(),pid:a.attr("data-pid")};layer.confirm("你确定要置顶么？",{icon:3},function(d){layer.close(d),a.ajax(U("moveTop"),function(a){0===a.code&&b.refresh(),b.alt(a)},c,"post")}),this.data("click",1)}),{init:d}});