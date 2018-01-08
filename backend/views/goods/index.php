<form method="get" action="" class="form-control-static">
   <input type="text" name="name" placeholder="请输入商品名称">
    <input type="text" name="sn"  placeholder="请输入货号">
    <input type="text" name="minPrice" placeholder="最低价格" >
    <input type="text" name="maxPrice"  placeholder="最高价格">
    <input type="submit" value="搜索">
</form>
<br>
<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>商品名称</td>
        <td>货号</td>
        <td>logo</td>
        <td>商品分类</td>
        <td>品牌</td>
        <td>市场价格</td>
        <td>商品价格</td>
        <td>库存</td>
        <td>是否在售</td>
        <td>状态</td>
        <td>排序</td>
        <td>创建时间</td>
        <td>浏览次数</td>
        <td>操作</td>
    </tr>
    <?php foreach($goodsList as $goods):?>
    <tr>
        <td><?=$goods->id?></td>
        <td><?=$goods->name?></td>
        <td><?=$goods->sn?></td>
        <td><img src="<?=$goods->logo?>" width="50px"></td>
        <td><?=$array[$goods->goods_category_id]?></td>
        <td><?=$arr[$goods->brand_id]?></td>
        <td><?=$goods->market_price?></td>
        <td><?=$goods->shop_price?></td>
        <td><?=$goods->stock?></td>
        <td><?=$goods->is_on_sale==1?'在售':'下架'?></td>
        <td><?=$goods->status==1?'正常':'回收站'?></td>
        <td><?=$goods->sort?></td>
        <td><?=date("Y-m-d H:i:s",$goods->create_time)?></td>
        <td><?=$goods->view_times?></td>
        <td><?=\yii\bootstrap\Html::a('相册',['goods/img','id'=>$goods->id])?>|<?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$goods->id])?>|<?=\yii\bootstrap\Html::a('删除','javascript:;',['class'=>'delete','id'=>$goods->id])?>|<?=\yii\bootstrap\Html::a('预览',['goods/preview','id'=>$goods->id])?></td>
    </tr>
    <?php endforeach ?>
    <tr><td colspan="15"><?=\yii\bootstrap\Html::a('添加商品',['goods/add'])?></td></tr>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
$url=\yii\helpers\Url::to(['goods/delete']);
$js=<<<JS
    $("#table").on('click','tr td .delete',function(){
        if(confirm("您确定要删除吗？")){
            //获取当前商品id
            var id=$(this).attr('id');
           //删除当前行
           $(this).closest('tr').remove();
           //发送ajax请求删除数据库中的数据
           $.getJSON('$url?id='+id,function(data){
               
           })
        }
    })
JS;

$this->registerJs($js);
