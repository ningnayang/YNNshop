<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>权限名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($permissions as $permission):?>
        <tr>
            <td><?=$permission->name?></td>
            <td><?=$permission->description?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['rbac/edit','name'=>$permission->name])?><?=\yii\bootstrap\Html::a('删除','javascript:;',['class'=>'delete','name'=>$permission->name])?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?=\yii\bootstrap\Html::a('添加权限',['rbac/add'])?>
<?php
/********************************DATATABLES插件**********************************************************/
//>>1.注册需要的css和js文件
$this->registerCssFile("@web/DataTables/DataTables/css/jquery.dataTables.css");
$this->registerJsFile("@web/DataTables/DataTables/js/dataTables.jqueryui.js",[
    "depends"=>\yii\web\JqueryAsset::className(),
]);
$this->registerJsFile("@web/DataTables/DataTables/js/jquery.dataTables.js",[
    "depends"=>\yii\web\JqueryAsset::className(),
]);
//>>2.注册js
$url=\yii\helpers\Url::to(['rbac/delete']);
$js = <<<JS
  $(function () {
        $('#table_id_example').DataTable({
           language: {
    "sProcessing": "处理中...",
    "sLengthMenu": "显示 _MENU_ 项结果",
    "sZeroRecords": "没有匹配结果",
    "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
    "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
    "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
    "sInfoPostFix": "",
    "sSearch": "搜索:",
    "sUrl": "",
    "sEmptyTable": "表中数据为空",
    "sLoadingRecords": "载入中...",
    "sInfoThousands": ",",
    "oPaginate": {
        "sFirst": "首页",
        "sPrevious": "上页",
        "sNext": "下页",
        "sLast": "末页"
    },
    "oAria": {
        "sSortAscending": ": 以升序排列此列",
        "sSortDescending": ": 以降序排列此列"
    }
} 
        });
    });

 $("#table_id_example").on('click','tr td .delete',function(){
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

/********************************DATATABLES插件**********************************************************/