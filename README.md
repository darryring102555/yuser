# TS
TS基于thinkphp开发的集成框架;主要功能:1.集成前端模板;2.php只做数据提供;3.代码生成器;4.数据结构关系图
使用它将对你的项目开发提速

#前端入口
<!--# 作者--张世平 #-->
<!--
解说:TS表示Total station  全站通用的意思
      TS只是一个代号,我百度收索"全站通用"出来的,大家不要纠结这个TS是啥子
      作用:
        1.解决各个域名下的iframe嵌入问题,js调取等问题
        2.将数据与模板分离,模板全部可以静态化缓存,甚至数据也静态化(可以定时更新)
        3.模板渲染将全部在浏览器段执行,减轻服务器压力
        4.节省流量
        5.嵌入模式统一,方便管理维护
      注意事项
         1.#引用该模块会给全局生成一个TS对象和requirejs对象,引用时请不要把这两个变量名暂用
         2.#如果你的页面使用了define这个全局变量(即:其它的AMD、CMD、CommonJS模式),请不要引用require.min.js的min模式
            ---为了兼容我们更改了debug模式的requirejs(页面加载完执行)
         3.#在使用debug模式时整个模块会先将已经定义过的全局变量define存放到TS.youerDefine中,模块运行完后还原最初的define
         4.#如果跨域引用数据请求请使用get,或者jsonp,也可直接使用函数requirejs去get数据(数据必须遵循define规范)
-->

<!-- 模板渲染存放的div -->
<div id="test"></div>
<!-- 数据准备 -->
方式1:<script src="http://data.test.com/index.php/admin/index/index.html?script=TS.data"></script>(获到的具体内容跟方式2一样)
方式2:
    <script>
        var TS = {};
        TS.data = {"page":1,"title":"\u6211\u7684\u7f51\u7ad9","count":3,"handleDate":false,"data":[{"title":"Paint pots","quantity":8,"price":3.95},{"title":"Paint pots","quantity":400,"price":3.95},{"title":"Paint pots","quantity":90,"price":3.95}]};
    </script>
<!-- 模板渲染->显示页面 -->
<script src="http://static.test.com/tooljs/require.js/2.1.18/require.min.js" <!-- 如果你的页面使用了define这个全局变量(即:其它的AMD、CMD、CommonJS模式),请不要引用min模式 -->
            data-main="http://static.test.com/js/main.js?time=2015061603" <!-- 入口文件名,此处不需修改,只需修改time参数来刷新文件 -->
            edition="2015061603" <!-- 除入口文件的所有静态文件的版本控制 -->
            module-name="Admin" <!-- 以下三个参数必须配置正确,否则不会获取到准确的模板更事件绑定 -->
            controller-name="Index"
            action-name="index"
            data-name="TS.data" <!-- 渲染页面数据的变量名,必须更数据准备的变量名一致(默认是:data) -->
            hand-date="true" <!-- 是否还需要对数据继续处理(不处理请设置false) -->
            target="test" <!-- 模板渲染存放的位置(通过id查找),默认是body -->
            id="entrance" <!-- 不可更改 -->
            >
</script>