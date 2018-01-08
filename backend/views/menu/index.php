<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>菜单名称</td>
        <td>路由</td>
        <td>排序</td>
        <td>操作</td>
    </tr>
    <?php foreach($menuList as $menu):?>
        <tr>
            <td><?=$menu->id?></td>
            <td><?=$menu->label?></td>
            <td><?=$menu->url?></td>
            <td><?=$menu->sort?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$menu->id])?>|<?=\yii\bootstrap\Html::a('删除','javascript:;',['class' => 'delete','id'=>$menu->id])?></td>
        </tr>
    <?php endforeach?>
</table>
<?php
$url=\yii\helpers\Url::to(['menu/delete']);
$js=<<<JS
    $("#table").on('click','tr td .delete',function(){
        if(confirm("您确定要删除吗?")){
            //获取当前行的id
             var id=$(this).attr('id');
        //删除当前行
             $(this).closest('tr').remove();
        //发送ajax请求删除
            $.getJSON('$url?id='+id,function(data){
                
            })
            }
    
    })
JS;

$this->registerJs($js);
