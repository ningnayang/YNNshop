<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'logo')->hiddenInput();
//图片回显
//echo $form->field($model,'imgFile')->fileInput();
//===================web uploader文件上传================
//注册css和js文件
$this->registerCssFile('@web/webuploader/webuploader.css');
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
$url=\yii\helpers\Url::to(['brand/upload']);
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
       console.log(response);
       $("#img").attr('src',response.url);
       //将图片地址直接保存在隐藏文本域中，随着表单提交一起提交
       $("#brand-logo").val(response.url);
      
});

JS;
$this->registerJs($js);



echo $form->field($model,'status',['inline'=>1])->radioList(['0'=>'隐藏','1'=>'正常']);
echo \yii\bootstrap\Html::submitButton('提交');
\yii\bootstrap\ActiveForm::end();
