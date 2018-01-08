<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['users/index']);
\yii\bootstrap\ActiveForm::end();

