
<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'into')->textarea();
echo $form->field($model,'parent_id')->hiddenInput();
/*********************************************加载数需要的css和js文件******************************************/
$this->registerCssFile("@web/zTree/css/zTreeStyle/zTreeStyle.css");
$this->registerJsFile("@web/zTree/js/jquery.ztree.core.js",[
    "depends"=>\yii\web\JqueryAsset::className(),
]);
echo
<<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;
$id=$model->id?$model->id:0;
$nodes=\backend\models\GoodsCategory::getNodes();
$js=<<<JS
 var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
           callback: {//为节点注册点击事件
                onClick: function(event, treeId, treeNode){
                    //获取被点击节点的id,赋值给input框goodscategory-parent_id
                    $("#goodscategory-parent_id").val(treeNode.id);
                }
            }
         

        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
        // $(document).ready(function(){
          zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        // });//因为depands已经解决了最后再加载的问题
        //如果层级过多,展开所有节点

           zTreeObj.expandAll(true);
       //节点回显选中
       var node=zTreeObj.getNodeByParam('id','$model->id',null);
        zTreeObj.selectNode(node);
       
        
       
       
JS;


$this->registerJs($js);
/*********************************************加载数需要的css和js******************************************/
echo \yii\bootstrap\Html::submitButton('提交');
\yii\bootstrap\ActiveForm::end();


