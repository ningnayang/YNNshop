<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'oldPassword')->passwordInput();
echo $form->field($model,'newPassword')->passwordInput();
echo $form->field($model,'rePassword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('确认修改');
\yii\bootstrap\ActiveForm::end();
