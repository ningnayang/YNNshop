<?php
namespace frontend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;
use common\models\SphinxClient;

class ListController extends Controller{
    public $enableCsrfValidation=false;
    /**
     * 显示商品分类下的商品
     */
    public function actionIndex(){
        $keywords=\Yii::$app->request->get('keywords');
        $id=\Yii::$app->request->get('id');
        //找到该条数据
        $goods=GoodsCategory::findOne(['id'=>$id]);
        if($goods->depth==2){
            //三级分类
            $ids=[$id];
        }else{
            //不是三级分类，是一级分类 或者二级分类
            $categorys=$goods->children()->select('id')->andWhere(['depth'=>2])->asArray()->all();
            $ids=ArrayHelper::map($categorys,'id','id');
        }
        $query=Goods::find()->where(['in','goods_category_id',$ids]);
        //完成分页
        $pager=new Pagination([
            'totalCount'=>$query->count(),//总记录数
            'defaultPageSize'=>3
        ]);
        $goodsList=$query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index',['goodsList'=>$goodsList,'pager'=>$pager]);
    }
    /**
     * 完成商品 sphinx 分词搜索功能
     */
    public function actionSearch($keywords){
        $cl = new SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);
        $cl->SetConnectTimeout ( 10 );
        $cl->SetArrayResult ( true );
        $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);
        $cl->SetLimits(0, 1000);
        //$info = '索尼电视';//关键字
        $res = $cl->Query($keywords, 'mysql');//shopstore_search
        $ids=[];
        if(isset($res['matches'])){
            foreach($res['matches'] as $match ){
                $ids[]=$match['id'];
            }
        }
        $query=Goods::find()->where(['in','id',$ids]);
        $pager=new Pagination([
            'totalCount'=>$query->count(),//总记录数
            'defaultPageSize'=>3
        ]);
        $goodsList=$query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index',['goodsList'=>$goodsList,'pager'=>$pager]);

    }


    /**
     * 显示商品详情
     */
    public function actionIntro($id){
        //获取该商品信息
        $goods=Goods::findOne(['id'=>$id]);
        //获取该商品的详情
        $goodsIntro=GoodsIntro::findOne(['goods_id'=>$id]);
        //获取该商品的相册图片
        $goodsGallery=GoodsGallery::find()->select(['path'])->where(['goods_id'=>$id])->all();
        //获取相册的第一张图片
        if($goodsGallery){
            $first=$goodsGallery[0]->path;
        }else{
            //没有相册就用商品的logo图片
            $first=$goods->logo;
        }
        //删除相册的第一张照片
        array_shift($goodsGallery);
        //获取所有品牌
        $brands=Brand::find()->select(['id','name'])->asArray()->all();
        $options=[];
        foreach($brands as $brand){
            $options[$brand['id']]=$brand['name'];
        }
        //让商品的浏览次数加1

        if($goods->view_times){
            $goods->view_times+=1;
        }else{
            $goods->view_times=1;
        }
        $views=$goods->view_times;
        $goods->save();
        return $this->render('goods',['goods'=>$goods,'goodsIntro'=>$goodsIntro,'goodsGallery'=>$goodsGallery,'options'=>$options,'first'=>$first,'views'=>$views]);
    }
/*
 * 添加购物车成功页面
 */
public function actionAddToCart($goods_id,$amount){
    //未登录。购物车数据保存到cookie中
    if(\Yii::$app->user->isGuest){
        //>>1.读取cookie中的购物车信息
            $cookies=\Yii::$app->request->cookies;
            if($cookies->has('cart')){
                //如果购物车中有购物信息,取出购物信息
                $value=$cookies->getValue('cart');
                $cart=unserialize($value);
            }else{
                //如果购物车中没有购物信息
                $cart=[];
            }
        //>>2.将商品保存至购物车中
            //如果商品存在，则让商品数量累加，以免覆盖
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id]+=$amount;//['1'=>1,'2'=>2]
            }else{
                //商品不存在，直接存数量
                $cart[$goods_id]=$amount;
            }
        //>>3.将购物车数据保存在cookie中
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie->name='cart';
            $cookie->value=serialize($cart);
            $cookies->add($cookie);
    }else{
        //已经登录,购物车保存至数据表
        $member_id=\Yii::$app->user->identity->id;
        //将cookie中的商品保存至数据库中
        $cookies=\Yii::$app->request->cookies;
        if($cookies->has('cart')){
            $value=$cookies->getValue('cart');
            $goodsList=unserialize($value);
            //如果数据库之前没有该商品数据，就将cookie中的数据保存至数据库
            $ids=array_keys($goodsList);
            foreach($ids as $id){
                $gd=Cart::findOne(['goods_id'=>$id]);
                if(!$gd){
                    $goodsCart=new Cart();
                    $goodsCart->goods_id=$id;
                    $goodsCart->amount=$goodsList[$id];
                    $goodsCart->member_id=$member_id;
                    $goodsCart->save();
                }else{
                    //数据库存在这条商品数据，让其数量累加
                    $gd->amount+=$goodsList[$id];
                    $gd->save();
                }

            }
            //将cookie中的数据保存至数据库后，清除cookie中的值
            $cookies=\Yii::$app->response->cookies;
            $cookies->remove('cart');
        }
        //登录后添加商品 如果商品存在，让商品数量累加
        $goods=Cart::findOne(['goods_id'=>$goods_id]);
        if($goods){
            $goods->amount+=$amount;
            $goods->save();
        }else{
            //不存在商品
            $cart=new Cart();
            $cart->goods_id=$goods_id;
            $cart->amount=$amount;
            $cart->member_id=$member_id;
            $cart->save();
        }
    }

    //跳转至购物车页面
    return $this->redirect(['list/cart']);
}

/**
 * 购物车页面
 */
public function actionCart(){
    //判断用户是否登录
    //>>1.未登录，购物车数据从cookie中获取
    if(\Yii::$app->user->isGuest){
        $cookies=\Yii::$app->request->cookies;
        $value=$cookies->getValue('cart');
        $cart=unserialize($value);
        $ids=array_keys($cart);
    }else{
        //已经登录，购物车数据从数据表中获取
        $member_id=\Yii::$app->user->id;
        $goods=Cart::find()->where(['member_id'=>$member_id])->all();
        $ids=[];
        //处理cart;
        $cart=[];
        foreach($goods as $value){
            $ids[]=$value->goods_id;
            $cart[$value->goods_id]=$value->amount;
        }
    }
    $goodsList=Goods::find()->where(['in','id',$ids])->all();
    return $this->render('cart',['goodsList'=>$goodsList,'cart'=>$cart]);

}

/**
 * 完成修改商品数量的功能
 */
public function actionChange(){
    //获取要修改的商品及修改后的数量
    $goods_id=\Yii::$app->request->post('goods_id');
    $amount=\Yii::$app->request->post('amount');
    if($amount){
        //如果金额不为0，不是删除操作，即修改金额
        if(\Yii::$app->user->isGuest){
            //如果未登录，修改cookie中的商品数量
            $cookies=\Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $values=$cookies->getValue('cart');
                $cart=unserialize($values);
            }else{
                $cart=[];
            }
            $cart[$goods_id]=$amount;//修改商品的数量
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie-> name='cart';
            $cookie->value=serialize($cart);
            $cookies->add($cookie);
        }else{
            //如果登录，就修改数据库中的数据
            $cart=Cart::findOne(['goods_id'=>$goods_id]);
            $cart->updateAttributes(['amount'=>$amount]);
        }
    }else{
        //金额为0 是删除操作
        //如果已经登录
        if(!\Yii::$app->user->isGuest){
            //查出该购物车数据
            $cart=Cart::findOne(['goods_id'=>$goods_id]);
            //删除这条数据
            $result=$cart->delete();
            if($result){
                echo Json::encode('true');
            }else{
                echo Json::encode('false');
            }
        }else{
            //如果没有登录，删除cookie中的数据
            $cookies=\Yii::$app->request->cookies;
            $carts=$cookies->getValue('cart');
            $carts=unserialize($carts);
            $ids=array_keys($carts);
            foreach($ids as $id){
                if($id==$goods_id){
                    unset($carts[$id]);
                }
            }
            //将新数组重新放回cookie
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie->name='cart';
            $cookie->value=serialize($carts);
            $cookies->add($cookie);
            echo Json::encode('true');
        }
    }

}
    /**
     * 确认订单页面
     */
    public function actionOrder()
    {
        //POST进来完成订单的添加功能
        //未登录，引导用户登录
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['member/login']);
        } else {
            //用户登录
            //获取用户地址
            $member_id = \Yii::$app->user->id;
            $address = Address::find()->where(['member_id' => $member_id])->orderBy('default desc,id asc')->all();
            //获取送货方式
            $deliveries = Order::$deliveries;
            //获取支付方式
            $pays = Order::$pays;
            //获取购物车商品信息
            $carts = Cart::find()->where(['member_id' => $member_id])->all();
            //商品数量 商品总金额
            $total = 0;
            $count = 0;
            foreach ($carts as $cart) {
                $goods = Goods::findOne(['id' => $cart->goods_id]);
                $cart->logo = $goods->logo;
                $cart->price = $goods->shop_price;
                $total += $cart->amount;
                $count += ($goods->shop_price) * ($cart->amount);
            }
            //POST方式进来保存订单的完成功能
            $request = new Request();
            if ($request->isPost) {
                $order = new Order();
                $order->load($request->post(), '');
                //在保存订单前开启事务
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($order->validate()) {
                        //order表
                        //保存 地址相关字段信息
                        $address = Address::findOne(['id' => $order->address_id]);
                        $order->member_id = $member_id;
                        $order->name = $address->username;
                        $order->province = explode(',', $address->address)[0];
                        $order->city = explode(',', $address->address)[1];
                        $order->area = explode(',', $address->address)[2];
                        $order->address = $address->address_detail;
                        $order->tel = $address->telephone;
                        //保存 配送方式相关信息
                        $delivery = Order::$deliveries[$order->delivery];
                        $order->delivery_id = $order->delivery;
                        $order->delivery_name = $delivery[0];
                        $order->delivery_price = $delivery[1];
                        //保存付款方式相关信息
                        $order->payment_id = $order->pay;
                        $order->payment_name = Order::$pays[$order->pay][0];
                        //商品状态
                        $order->status = 2;
                        //其他
                        $order->trade_no = $member_id;
                        $order->create_time = time();
                        $order->save();
                        $order_id = \Yii::$app->db->lastInsertID;
                        $order=Order::findOne(['id'=>$order_id]);
                        $order->total=0;
                        //order_goods表
                        //获取用户购物车数据
                        $carts = Cart::find()->where(['member_id' => $member_id])->all();
                        foreach ($carts as $cart) {
                            //找到购物车中的商品对应的商品信息
                            $goods = Goods::findOne(['id' => $cart->goods_id]);
                            //判断商品库存是否足够
                            //库存足够保存订单商品信息
                            if ($goods->stock>=$cart->amount) {
                                $order_goods = new OrderGoods();
                                $order_goods->order_id = $order_id;
                                $order_goods->amount = $cart->amount;
                                $order_goods->goods_id = $cart->goods_id;
                                $order_goods->goods_name = $goods->name;
                                $order_goods->logo = $goods->logo;
                                $order_goods->price = $goods->shop_price;
                                $order_goods->total = ($cart->amount) * ($goods->shop_price);
                                $order_goods->save();
                                //扣减库存
                                $goods->stock -= $cart->amount;
                                $goods->save(false);
                                //处理订单商品总金额
                                $order->total += $order_goods->total;
                            } else {
                                //库存不足够 抛出异常
                                throw new Exception('商品库存不够，请修改购物车');
                            }

                        }
                        //处理订单总结额 商品总金额加运费
                        $order->total += $order->delivery_price;
                        $order->save();
                        //操作成功后清除用户的购物车数据
                        Cart::deleteAll(['member_id' => $member_id]);
                        //提交事务
                        $transaction->commit();
                        return $this->redirect(['list/order-success']);
                    }
                } catch (Exception $e) {
                    //回滚事务
                    $transaction->rollBack();
                    //跳转至购物车页面
                    return $this->redirect(['list/cart']);
                }

            }
            //GET方式进来完成显示提交订单页面的功能
            return $this->render('order', ['address' => $address, 'pays' => $pays, 'deliveries' => $deliveries, 'carts' => $carts, 'total' => $total, 'count' => $count]);
        }
    }

        /**
         * 完成订单成功页面的功能
         */
        public function actionOrderSuccess(){
            return $this->render('success');
        }

        /**
         * 完成订单列表状态功能
         */
        public function actionOrderList(){
            //获取该用户的所有订单 按日期降序
            $member_id=\Yii::$app->user->id;
            $orderList=Order::find()->where(['member_id'=>$member_id])->orderBy('create_time desc')->all();
            //获取相关订单的商品详情
            foreach($orderList as $order){
                $goodsList=OrderGoods::find()->where(['order_id'=>$order->id])->orderBy('')->all();
                foreach($goodsList as $key=>$goods){
                    $order->goodsPics[]=$goods->logo;
                    //图片大于3张 跳出循环
                   if($key>=2){
                        break;
                    }
                }


            }
            return $this->render('list',['orderList'=>$orderList]);
        }
        /**
         * 完成订单的删除功能
         */
        public function actionDeleteOrder($id){
            //根据id找到订单数据
            $order=Order::findOne(['id'=>$id]);
            //删除这条数据
            $order->delete();
            //找到订单下的商品数据 删除
            OrderGoods::deleteAll(['order_id'=>$id]);

        }
        /**
         * 情况：高并发情况下如果使用原来的库存处理方式，可能会出现用户同时下订单，同时判断库存的情况，
         *       但是sql扣减库存有先后顺序，所以会出现超卖问题
         * 问题：大并发情况下结局商品超卖问题?
         * 注意：1：大并发 既然是大并发最好使用redis来处理
         *       2：热门商品 如秒杀
         * 以上两种情况都可以使用redis来解决商品超卖问题
         */
        public function actionOverflow(){
                $goods_id=8;//商品id,假数据，到时候根据实际情况处理
                $stock=20;//商品库存，写的假数据,到时候根据实际情况处理
            //>>1.将商品库存保存在redis中
                $redis=new \Redis();
                $redis->set('stock_'.$goods_id,$stock);//注意写操作时要更新redis中的库存
            //>>2.保存订单
                //开启事务
                $transaction=\Yii::$app->db->beginTransaction();
                try{
                //保存订单
                $order=new Order();
                $order->save();
                $amount=6;//订单中商品数量 假数据 到时候根据实际情况处理
                //遍历购物车，保存订单中的商品信息
                foreach($carts as $cart){
                    //先扣减库存再判断
                    $result=$redis->decrBy('stock_'.$goods_id,$amount);
                    //保存商品扣减的库存，以便后面做事务回滚时使用
                    $redis->hSet('reduce_'.$order_id,$goods_id,$amount);//等同于$reduce_$order_id[$goods_id]=$amount
                    if($result<0){
                        //库存不足 抛出异常
                        throw new Exception("库存不足！");
                    }else{
                        //库存足够 保存商品信息
                        //扣减数据库商品库存
                        //清空购物车
                    }

                }

            }catch (Exception $e){
                //try中有某些操作失败 回滚事务
                $transaction->rollBack();
                //恢复redis中扣减的库存
                $reduce=$redis->hGetAll('reduce_'.$order_id);//等同于$reduce_$order_id['$goods_id'=>$amount];$arr['1'=>2,'2'=>3]
                foreach($reduce as $id=>$num){
                    //$id 哪个商品 $num 被扣减多少
                    $redis->incrBy('stock_'.$id,$num);
                }
            }

        }

    }
