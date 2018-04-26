/**
 * Created by zhangshiping on 15-8-6.
 */
var interfaces = [
{
        type: '用户类', //接口分类
        interface: [ //分类下的所有接口
            {
                name: '前端登录', //接口名称
                url: 'Api/User/loginHome/ajax/1', //接口地址
                description: '用户前端登录', //接口描述
                method: 'post', //提交方式
                paramsjson:false, //参数json化提交
                jsonp:false, //jsonp跨越请求
                params: [ //接口参数
                    {
                        name: 'uname', //参数名
                        info: '用户名', //参数说明
                        'type': '字符串', //参数类型
                        example: 'zsping1989', //参数事例
                        required: true //是否为必填参数
                    },
                    {name: 'password',info: '密码','type': '字符串',example: '123456',required: true},
                    {name: 'verify',info: '验证码','type': '字符串',example: 'ABCD',required: true},
                    {name: 'remember',info: '记住密码','type': '字符串',example: '1',required: false}
                ],
                result_info: { //返回值说明
                    'code':'状态码',
                    'msg':'提示信息'
                }
            },{
                name: '退出登录',
                url: 'Api/User/loginOut/ajax/1',
                description: '退出用户登录',
                method: 'get',
                paramsjson:false,
                jsonp:false,
                params: [],
                result_info: { //返回值说明
                    'code':'状态码',
                    'msg':'提示信息'
                }
            },{
                name: '注册用户',
                url: 'Api/User/signIn/ajax/1',
                description: '用户信息注册',
                method: 'post',
                paramsjson:false,
                jsonp:false,
                params: [
                    {name: 'uname',info: '用户名','type': '字符串',example: 'zsping1989',required: true},
                    {name: 'password',info: '密码','type': '字符串',example: '123456',required: true},
                    {name: 'repassword',info: '确认登录密码','type': '字符串',example: '123456',required: true},
                    {name: 'phone',info: '手机号码','type': '数字',example: '13699411148',required: true},
                    {name: 'email',info: '电子邮箱','type': '字符串',example: '214986304@qq.com',required: true}
                ],
                result_info: { //返回值说明
                    'code':'状态码',
                    'msg':'提示信息'
                }
            },{
                name: '修改用户密码',
                url: 'Api/User/changePassword/ajax/1',
                description: '修改用户密码',
                method: 'post',
                paramsjson:false,
                jsonp:false,
                params: [
                    {name: 'old_password',info: '原密码','type': '字符串',example: '123456',required: true},
                    {name: 'password',info: '原密码','type': '字符串',example: '123456',required: true},
                    {name: 'repassword',info: '确认登录密码','type': '字符串',example: '123456',required: true}
                ],
                result_info: { //返回值说明
                    'code':'状态码',
                    'msg':'提示信息'
                }
            }
        ]
    }
];
