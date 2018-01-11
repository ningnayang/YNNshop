<?php
namespace console\controllers;

use backend\models\Goods;
use frontend\models\Order;
use yii\console\Controller;

class TaskController extends Controller{
    //设置定时清理订单
    public function actionClean(){
        //临时修改php配置 脚本执行时间超过30秒不执行
        set_time_limit(0);//改为永久执行脚本时间
        while(true){
            //创建时间超过24小时 并且状态是待付款
            $orders=Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-24*3600])->all();
            //将这些订单的状态改为已取消 0
            foreach($orders as $order){
                $order->status=0;
                $order->save(false);
                //将商品数量返还库存
                $goodsList=Goods::find()->where(['order_id'=>$order->id])->all();
                foreach($goodsList as $goods){
                    Goods::updateAllCounters(['stock'=>$goods->amount],['id'=>$goods->goods_id]);
                }
            }
            //1秒中执行1次
           /* echo iconv('utf-8','gbk',"清理完成").date("H:i:s");
            echo "\n";*/
            sleep(1);
        }
    }
}