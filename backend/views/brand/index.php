<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>品牌名称</td>
        <td>简介</td>
        <td>logo</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($brandList as $brand):?>
    <tr>
        <td><?=$brand->id?></td>
        <td><?=$brand->name?></td>
        <td><?=$brand->intro?></td>
        <td><img src="<?=$brand->logo?>" width="50px"></td>
        <td><?=$brand->sort?></td>
        <td><?=$brand->status==0?'隐藏':'正常'?></td>
        <td><?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brand->id])?>|<?=\yii\bootstrap\Html::a('删除','javascript:;',['class'=>'delete','id'=>$brand->id])?></td>
    </tr>
    <?php endforeach ?>
    <tr><td colspan="7"><?=\yii\bootstrap\Html::a('添加品牌',['brand/add'])?></td></tr>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);?>
<?php
/**
 * @var  $this yii\web\View
 */
$url=\yii\helpers\Url::to(['brand/delete']);
$js=<<<JS
    $('#table').on('click','tr td .delete',function(){
       if(confirm('您确定要删除吗')){
         //>>1.获取当前的id 
            var id=$(this).attr('id');
            $(this).closest('tr').remove();//删除页面数据
        //>>2.将id传过去修改该条数据的状态值
            $.getJSON('$url?id='+id,function(data){
                
            })
        }
        
    })
JS;

$this->registerJs($js);



