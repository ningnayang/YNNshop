<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'content')->widget(\common\widgets\ueditor\Ueditor::className());
echo $form->field($model,'article_category_id')->dropDownList($options);
echo $form->field($model,'status',['inline'=>1])->radioList(['0'=>'隐藏','1'=>'正常']);
echo \yii\bootstrap\Html::submitButton('提交');
\yii\bootstrap\ActiveForm::end();

