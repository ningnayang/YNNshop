<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>文章名称</td>
        <td>简介</td>
        <td>文章分类id</td>
        <td>状态</td>
        <td>创建时间</td>
        <td>操作</td>
    </tr>
    <?php foreach($articleList as $article):?>
    <tr>
        <td><?=$article->id?></td>
        <td><?=$article->name?></td>
        <td><?=$article->intro?></td>
        <td><?=$arr[$article->article_category_id]?></td>
        <td><?=$article->status==1?'正常':'隐藏'?></td>
        <td><?=date("Y-m-d H:i:s",$article->create_time)?></td>
        <td><?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$article->id])?>|<?=\yii\bootstrap\Html::a('删除','javascript:;',['class' => 'delete','id'=>$article->id])?></td>
    </tr>
    <?php endforeach?>
    <tr><td colspan="7"><?=\yii\bootstrap\Html::a('添加文章',['article/add'])?></td></tr>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);

//完成删除功能
$url=\yii\helpers\Url::to(['article/delete']);
$js=<<<JS
//给删除按钮添加点击事件
$("#table").on('click','tr td .delete',function(){
    
    if(confirm('您确定要删除吗？')){
        //获取当前id值
        var id=$(this).attr('id');
        //删除页面上的相关行数据
        $(this).closest('tr').remove();
       //发送ajax请求，将当前数据的状态改为-1
       $.getJSON('$url?id='+id,function(data){
           
       })
    }
    
})
JS;

$this->registerJs($js)
?>