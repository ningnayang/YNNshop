<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sn')->textInput();
/*******************************************Logo图片 文件上传插件********************************************************/
echo $form->field($model,'logo')->hiddenInput();//隐藏文本域
$this->registerCssFile('@web/webuploader/webuploader.css');//注册Css和js文件
$this->registerJsFile('@web/webuploader/webuploader.js',[
    //指定该文件依赖于jquery,即在jquery之后加载
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo
<<<HTML
<!--dom结构部分-->
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<img id="img" width="100px" src="$model->logo"/>
HTML;
$url=\yii\helpers\Url::to(['goods/upl']);
$js=<<<JS
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: '/webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '$url',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});
uploader.on( 'uploadSuccess', function( file ,response) {
       //图片上传成功后回显图片，提高用户体验
       //console.log(response);
       $("#img").attr('src',response.url);
       //将图片地址直接保存在隐藏文本域中，随着表单提交一起提交
       $("#goods-logo").val(response.url);
});

JS;
$this->registerJs($js);
/*******************************************文件上传插件**************************************************/

/******************************************商品详情 富文本编辑器插件******************************************/
echo $form->field($model,'content')->widget(\common\widgets\ueditor\Ueditor::className());//富文本编辑器插件
/******************************************商品详情 富文本编辑器插件******************************************/

/*******************************************商品分类 ztree树插架***************************************************************/
echo $form->field($model,'goods_category_id')->hiddenInput();
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
$nodes=\backend\models\GoodsCategory::getNodes();
$js1=<<<JS
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
                    $("#goods-goods_category_id").val(treeNode.id);
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
            var node=zTreeObj.getNodeByParam('id','$model->goods_category_id',null);
            zTreeObj.selectNode(node);
       
        
       
       
JS;


$this->registerJs($js1);
/*******************************************商品分类 ztree树插架***************************************************************/
echo $form->field($model,'brand_id')->dropDownList($options);
echo $form->field($model,'market_price')->textInput();
echo $form->field($model,'shop_price')->textInput();
echo $form->field($model,'stock')->textInput();
echo $form->field($model,'is_on_sale',["inline"=>1])->radioList(["0"=>'下架',"1"=>"在售"]);
echo $form->field($model,'status',["inline"=>1])->radioList(["0"=>'回收站',"1"=>"正常"]);
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交');
\yii\bootstrap\ActiveForm::end();
