<h1><?=$model->name?></h1>
<?php
echo $model->content;
?>
<?php foreach($model->pics as $pic): ?>
    <p><img src="<?=$pic['path']?>"></p>
<?php endforeach ?>
