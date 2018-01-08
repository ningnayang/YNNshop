<?php
$form=\yii\bootstrap\ActiveForm::begin();
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
<!--
<table id="table">
</table>-->

HTML;
$id=$model->id;
$imgUrl=Yii::getAlias("@webroot")."/Upload/goods".$id."/";
$url=\yii\helpers\Url::to(['goods/upload','id'=>$model->id]);
$js=<<<JS
var id=$model->id;
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
       //$("#img").attr('src',response.url);
       //图片上传成功后追加一行img标签到table中回显
        $("<tr data_id="+response.gaid+" id="+id+"><td><img src="+response.url+" ></td><td><a href='#' >删除</a></td></tr>").appendTo($("#table"));
       
       
});

JS;
$this->registerJs($js);
$address=\yii\helpers\Url::to(['goods/del']);
$js1=<<<JS
    $("#table").on("click",'tr td a',function(){
       if(confirm("您确定要删除吗?")){
           //>>1.找到当前的商品id和相册id
               var id=$(this) .closest("tr").attr("id");
               var data_id=$(this).closest("tr").attr("data_id");
                $(this).closest("tr").remove();
              //发送ajax请求删除该行；
              $.getJSON('$address?id='+id+'&did='+data_id,function(data){
                  
              })
       }
    })
JS;
$this->registerJs($js1);
\yii\bootstrap\ActiveForm::end();
?>
<table id="table" class="table ">
    <?php foreach($pics as $pic):?>
    <tr id="<?=$gid?>" data_id="<?=$pic->id?>"><td><img src="<?=$pic->path?>"></td><td><a  href="#" class="delete">删除</a></td></tr>
    <?php endforeach?>
</table>
