<?php
?>
<table class="table table-bordered " id="table">
    <tr>
        <td>id</td>
        <td>名称</td>
        <td>简介</td>
        <td>操作</td>
    </tr>
    <?php foreach($categoryList as $category):?>
    <tr>
        <td><?=$category->id?></td>
        <td><?=$category->name?></td>
        <td><?=$category->into?></td>
        <td><?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$category->id])?>|<?=\yii\bootstrap\Html::a('删除','javascript:;',['class'=>'delete','id'=>$category->id])?></td>
    </tr>
    <?php endforeach?>
    <tr><td colspan="4"><?=\yii\bootstrap\Html::a('添加分类',['goods-category/add'])?></td></tr>
</table>
<!--/*=\yii\widgets\LinkPager::widget([
        'pagination'=>$pager,
        'nextPageLabel'=>'下一页',
        'prevPageLabel'=>'上一页'
])*/
-->
<?php

$url=\yii\helpers\Url::to(['goods-category/delete']);
$js=<<<JS
        $('#table').on('click','tr td .delete',function(){
            if(confirm("您确定要删除吗？")){
           //>>1.获取当前节点的id
               var id=$(this).attr("id");
               var category=$(this).closest('tr');
          //>>2.根据节点查询下面是否还有子节点
             $.getJSON('$url?id='+id,function(data){
               if(data==1){
                   alert('不能删除！该分类下有商品和子分类存在');
               }else if(data==2){
                   alert("不能删除！该分类下有商品存在")
               }else if(data==3){
                   alert("不能删除！该分类下有子分类存在")
               }else{
                   //删除这一行数据
                   category.remove();
               }
             })
            }
           
        })
        

JS;

$this->registerJs($js);

