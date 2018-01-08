<?php
if(Yii::$app->user->identity){
    echo '欢迎'.Yii::$app->user->identity->username.'来到管理员中心',"<br>";
    echo  \yii\bootstrap\Html::a('退出登录',['users/logout']),"<br>";
    echo \yii\bootstrap\Html::a('修改密码',['users/password']);
}else{
    echo '游客您好';
    echo \yii\bootstrap\Html::a('登录',['users/login']);
}
?>
<table class="table table-bordered" id="table">
    <tr>
        <td>id</td>
        <td>用户名</td>
        <td>邮箱</td>
        <td>状态</td>
        <td>最后登录时间</td>
        <td>最后登录ip</td>
        <td>操作</td>
    </tr>
    <?php foreach($userList as $user):?>
    <tr>
        <td><?=$user->id?></td>
        <td><?=$user->username?></td>
        <td><?=$user->email?></td>
        <td><?=$user->status==1?'正常':'禁用'?></td>
        <td><?=date("Y-m-d H:i:s",$user->last_login_time)?></td>
        <td><?=$user->last_login_ip?></td>
        <td><?=\yii\helpers\Html::a('修改',['users/edit','id'=>$user->id])?><?=\yii\helpers\Html::a('删除','javascript:;',['class'=>'delete','id'=>$user->id])?></td>
    </tr>
    <?php endforeach?>
    <tr><td colspan="7"><?=\yii\helpers\Html::a('添加用户',['users/add','id'=>$user->id])?></td></tr>
</table>
<?php
$url=\yii\helpers\Url::to(['users/delete']);
$js=<<<JS
   $("#table") .on('click','tr td .delete',function(data){
       if(confirm("您确定法要删除吗？")){
           //获取当前行id
           var id=$(this).attr("id");
           //删除当前行
           $(this).closest("tr").remove();
          //发送ajax请求 删除数据库中的数据
          $.getJSON('$url?id='+id,function(){
              
          })
       }
   })
JS;

$this->registerJs($js);
