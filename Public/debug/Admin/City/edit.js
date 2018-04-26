define(['jquery','common','validate_cn'
        ,'ztree'                            ],function(j,f,v
        ,z                            ){

    var  bind = {};
    
        /* 数据赋值 */
    bind.checkd = f.checkd;

                    function showIconForTree(treeId, treeNode) {
            return !treeNode.isParent;
        };

        /* 树状结构数据显示 */
        bind.ztree = function(){
            var setting = {
                view: {
                    showIcon: showIconForTree,
                    showIcon:false
                },
                data: {
                    simpleData: {
                        enable: true,
                        pIdKey: "parent_id"
                    },
                    key:{
                        url:'javascript:void(0);'
                    }
                },
                callback:{
                    onClick:function(event,treeid,treeNode){
                        j('.ts-tree').val([treeNode.id]);
                    }
                }
            };

            var data = window[TS.config.data_var]["data"] || [];
            var zNodes = window[TS.config.data_var]['tree'];
            zNodes[0]['open']=1;
            j.fn.zTree.init(j("#tree"), setting, zNodes);
            var treeObj = j.fn.zTree.getZTreeObj("tree");
            var node =  treeObj.getNodeByParam('id',data['parent_id'],null);
            treeObj.selectNode(node);
        }
    /* 表单验证 */
    bind.verify = function(){
        j("#edit").validate({
            rules: {
                name:{required:true},parent_id:{number:true}            },
            messages: {
                name:{"required":"true"},parent_id:{"number":"true"}            },
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