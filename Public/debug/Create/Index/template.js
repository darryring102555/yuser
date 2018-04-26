/*TMODJS:{"version":"1.0.0"}*/
!function(){function a(a,b){return(/string|function/.test(typeof b)?h:g)(a,b)}function b(a,c){return"string"!=typeof a&&(c=typeof a,"number"===c?a+="":a="function"===c?b(a.call(a)):""),a}function c(a){return l[a]}function d(a){return b(a).replace(/&(?![\w#]+;)|[<>"']/g,c)}function e(a,b){if(m(a))for(var c=0,d=a.length;d>c;c++)b.call(a,a[c],c,a);else for(c in a)b.call(a,a[c],c)}function f(a,b){var c=/(\/)[^/]+\1\.\.\1/,d=("./"+a).replace(/[^/]+$/,""),e=d+b;for(e=e.replace(/\/\.\//g,"/");e.match(c);)e=e.replace(c,"/");return e}function g(b,c){var d=a.get(b)||i({filename:b,name:"Render Error",message:"Template not found"});return c?d(c):d}function h(a,b){if("string"==typeof b){var c=b;b=function(){return new k(c)}}var d=j[a]=function(c){try{return new b(c,a)+""}catch(d){return i(d)()}};return d.prototype=b.prototype=n,d.toString=function(){return b+""},d}function i(a){var b="{Template Error}",c=a.stack||"";if(c)c=c.split("\n").slice(0,2).join("\n");else for(var d in a)c+="<"+d+">\n"+a[d]+"\n\n";return function(){return"object"==typeof console&&console.error(b+"\n\n"+c),b}}var j=a.cache={},k=this.String,l={"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"},m=Array.isArray||function(a){return"[object Array]"==={}.toString.call(a)},n=a.utils={$helpers:{},$include:function(a,b,c){return a=f(c,a),g(a,b)},$string:b,$escape:d,$each:e},o=a.helpers=n.$helpers;a.get=function(a){return j[a.replace(/^\.\//,"")]},a.helper=function(a,b){o[a]=b},"function"==typeof define?define(function(){return a}):"undefined"!=typeof exports?module.exports=a:this.template=a,/*v:1*/
a("index",function(){"use strict";var a=this,b=a.$helpers,c=a.$escape,d="";return d+='<div class="ts-container"> <section class="content-header"> <h1> \u521b\u5efa\u4ee3\u7801 </h1> <ol class="breadcrumb"> <li><a href="#"><i class="fa fa-dashboard"></i> \u521b\u5efa\u4ee3\u7801</a></li> <li><a href="#"><i class="fa fa-mouse-pointer"></i> \u521b\u5efa\u4ee3\u7801</a></li> <li class="active">Data tables</li> </ol> </section> <section class="content"> <div class="row"> <div class="col-xs-12">  <div class="box"> <div class="box-header row"> <div class="col-sm-12"> <h1 style="text-align: center">\u6b22\u8fce\u4f7f\u7528\u4ee3\u7801\u751f\u6210\u5de5\u5177</h1> </div> </div>  <div class="box-body"> <div class="dataTables_wrapper form-inline dt-bootstrap" id="example1_wrapper"> <div class="row"> <div class="col-sm-12"> <div id="example1_length" class="dataTables_length"> <form action="',d+=c(b.F("U","create")),d+='" method="get"> <table class="table table-hover table-bordered"> <tr> <th>\u9009\u9879</th> <th>\u503c</th> <th>\u8bf4\u660e</th> </tr> <tr> <td>\u6a21\u5757\u540d</td> <td><input name="m_name" type="text"></td> <td>\u5fc5\u586b:\u7528\u4e8e\u4ee3\u7801\u751f\u6210\u8def\u5f84</td> </tr> <tr> <td>\u63a7\u5236\u5668\u540d</td> <td><input name="c_name" type="text"></td> <td>\u5fc5\u586b:\u7528\u4e8e\u4ee3\u7801\u751f\u6210\u8def\u5f84\u53caController\u6587\u4ef6;</td> </tr> <tr> <td>\u65b9\u6cd5\u540d</td> <td><input name="a_name" type="text" value="index"></td> <td>\u5fc5\u586b:\u7528\u4e8e\u4ee3\u7801\u751f\u6210\u8def\u5f84\u53ca\u751f\u6210\u524d\u7aefjs,\u6587\u4ef6\u7b49</td> </tr> <tr> <td>\u6570\u636e\u8868\u540d</td> <td><input name="t_name" type="text"></td> <td>\u5fc5\u586b:\u7528\u4e8e\u83b7\u53d6\u6570\u636e\u8868\u4fe1\u606f(\u4e0d\u9700\u8981\u524d\u7f00)</td> </tr> <tr> <td>title</td> <td><input name="title" type="text" value=""></td> <td>\u9009\u586b:\u9875\u9762\u7684\u4e8c\u7ea7title</td> </tr> <tr> <td>\u662f\u5426\u5fc5\u987b\u767b\u5f55\u624d\u80fd\u8bbf\u95ee:</td> <td> <input type="radio" name="mustLogin" checked="checked" value="1">&nbsp;\u662f&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="mustLogin" value="0">&nbsp;\u5426</td> <td>\u9009\u586b:\u6dfb\u52a0,\u4fee\u6539\u6570\u636e\u65f6\u9a8c\u8bc1\u6570\u636e</td> </tr> <tr> <td>\u662f\u5426\u521b\u5efa\u6570\u636eMODEL\u7c7b:</td> <td> <input type="radio" name="model" checked="checked" value="1">&nbsp;\u662f&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="model" value="0">&nbsp;\u5426</td> <td>\u9009\u586b:\u9009\u62e9\u662f\u5426\u751f\u6210Model\u7c7b</td> </tr> <tr> <td>\u662f\u5426\u521b\u5efa\u524d\u7aefmodel\u5904\u7406\u6570\u636e:</td> <td> <input type="radio" name="proscenium_model" value="1">&nbsp;\u662f&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="proscenium_model" checked="checked" value="0">&nbsp;\u5426</td> <td>\u9009\u586b:\u751f\u6210\u524d\u7aefmodel\u5728\u524d\u7aef\u5904\u7406\u6570\u636e</td> </tr> <tr> <td>\u662f\u5426\u524d\u7aef\u9a8c\u8bc1\u5b57\u6bb5:</td> <td> <input type="radio" name="validata" value="1">&nbsp;\u662f&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="validata" checked="checked" value="0">&nbsp;\u5426</td> <td>\u9009\u586b:\u6dfb\u52a0\u6216\u4fee\u6539\u6570\u636e\u65f6\u524d\u7aef\u9a8c\u8bc1\u5b57\u6bb5</td> </tr> </table> <br /> <div style="text-align: center"> <button type="button" class="btn btn-primary btn-large create">\u751f\u6210</button> <button type="reset" class="btn btn-info btn-large">\u91cd\u7f6e</button> </div> </form> </div> </div> </div> </div> </div>  </div>  </div>  </div>  </section> </div> ',new k(d)})}();