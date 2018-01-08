<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList(['0'=>'禁用','1'=>'正常']);
echo $form->field($model,'roles',['inline'=>1])->checkboxList($options);
echo \yii\bootstrap\Html::submitButton('提交',['users/index']);
\yii\bootstrap\ActiveForm::end();

