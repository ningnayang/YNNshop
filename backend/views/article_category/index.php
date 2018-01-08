<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>文章分类</td>
        <td>简介</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($articleList as $article):?>
    <tr>
        <td><?=$article->id?></td>
        <td><?=$article->name?></td>
        <td><?=$article->intro?></td>
        <td><?=$article->sort?></td>
        <td><?=$article->status?></td>
        <td><?=\yii\helpers\Html::a('修改',['article_category/edit','id'=>$article->id])?><?=\yii\helpers\Html::a('删除','javascript:;',['class'=>'delete','id'=>$article->id])?></td>
    </tr>
    <?php endforeach ?>
    <tr><td colspan="6"><?=\yii\helpers\Html::a('添加文章',['article_category/add'])?></td></tr>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
/**
 * @var $this yii\web\View
 */
$url=\yii\helpers\Url::to(['article_category/delete']);
$js=<<<JS
    $('#table').on('click','tr td .delete',function(){
        if(confirm("您确定要删除吗")){
        //>>1.获取当前id
            var id=$(this).attr('id');
        //>>2.删除当前行
            $(this).closest('tr').remove();
        //>>3.修改当前行的状态值为-1
            $.getJSON('$url?id='+id,function(data){
                
            })
        } 
            
    })
JS;
$this->registerJs($js);
?>

