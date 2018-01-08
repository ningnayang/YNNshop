<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'label')->textInput();
echo $form->field($model,'url')->dropDownList($routes);
echo $form->field($model,'parent_id')->dropDownList($arr);
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交');
\yii\bootstrap\ActiveForm::end();