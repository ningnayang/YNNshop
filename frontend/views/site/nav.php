<div class="topnav">
    <div class="topnav_bd w1210 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li><?=Yii::$app->user->isGuest?"游客":Yii::$app->user->identity->username?>你好！欢迎来到京西！<?=Yii::$app->user->isGuest?'[<a href="'.\yii\helpers\Url::to(['member/login']).'">登录</a>][<a href="'.yii\helpers\Url::to(['member/register']).'">免费注册</a>]':'[<a href="'.\yii\helpers\Url::to(['member/logout']).'">注销</a>]'?> </li>
                <li class="line">|</li>
                <li><a href="<?=\yii\helpers\Url::to(['list/order-list'])?>">我的订单</a></li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
