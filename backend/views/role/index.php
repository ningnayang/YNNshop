<table class="table table-bordered" id="table">
    <tr>
        <td>角色名称</td>
        <td>描述</td>
        <td>操作</td>
    </tr>
    <?php foreach($roles as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['role/edit','name'=>$role->name])?><?=\yii\bootstrap\Html::a('删除','javascript:;',['class'=>'delete','name'=>$role->name])?></td>
        </tr>
    <?php endforeach?>
    <tr><td colspan="4"><?=\yii\bootstrap\Html::a('添加角色',['role/add'])?></td></tr>
</table>
<?php
$url=\yii\helpers\Url::to(['role/delete']);
$js=<<<JS
    $("#table").on('click','tr td .delete',function(){
        if(confirm("您确定要删除吗")){
             //获取当前角色的名字
                var name=$(this).attr('name');
            //删除这一行
                $(this).closest('tr').remove();
            //发送ajax请求删除数据库中的数据
                $.getJSON('$url?name='+name,function(data){
                    
                })
                }
    })
JS;

$this->registerJs($js);

