<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'users/captcha',
    'template'=>'<div class="row"><div class="col-xs-1">{input}</div><div class="col-xs-1">{image}</div></div>'
]);
echo $form->field($model,'remember')->checkbox(["1"=>"记住我"]);
echo \yii\bootstrap\Html::submitButton('登录');
\yii\bootstrap\ActiveForm::end();
