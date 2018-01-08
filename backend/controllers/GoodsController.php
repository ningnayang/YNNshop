<?php
//命名空间
namespace  backend\controllers;

//创建商品控制器类
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Search;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
/*
 * 七牛云插件需要用到的
 * */
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class GoodsController extends Controller{
    public $enableCsrfValidation=false;//关闭验证,才能成功上传图片
    /**
     * 富文本编辑器插件
     */
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
    /**
     * 处理文件上传，相册上传成功后将图片上传至七牛云
     */
    public function actionUpload(){
        $id=$_GET['id'];
        //处理上传文件目录,分目录创建文件
//        $dirname="/Upload/goods".$id."/";
//        if(!is_dir($dirname)){
//            mkdir(\Yii::getAlias('@webroot').$dirname,0777,true);
//        }
        $img=UploadedFile::getInstanceByName('file');
        $filename='/upload/'.uniqid().'.'.$img->extension;
        if($img->saveAs(\Yii::getAlias('@webroot').$filename,0)){
            //+++++++++++++++++++++++文件本地上传成功，将文件上传至七牛云+++++++++++++++++++
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="ONQWIImIY4gjsrb530540cD7sFXR3fK4t6hPlHke";
            $secretKey = "_JiNy97QmSxfAE-jOpnEQtipQq-Q_xHF5vd9ZQMH";
            $bucket = "yangyii";
            $domain='http://p1bekxo6w.bkt.clouddn.com';//七牛云的地址
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$filename;
            // 上传到七牛后保存的文件名
            $key = $filename;
            $url="$domain/$key";//上传后七牛云的图片地址
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                //文件上传失败
                echo json_encode(['error'=>1]);
            } else {
                //将商品图片保存在商品相册中
                $goods_gallery=New GoodsGallery();
                $goods_gallery->goods_id=$id;
                $goods_gallery->path=$url;
                $goods_gallery->save();
                $gaid=\Yii::$app->db->getLastInsertID();
                //文件上传成功，返回七牛云的地址
                echo json_encode(['url'=>$url,'gaid'=>$gaid]);
            }

        }
    }
    /**
     * 处理文件上传，上传logo图片
     */
    public function actionUpl(){
        $img=UploadedFile::getInstanceByName('file');
        $filename='/upload/'.uniqid().'.'.$img->extension;
        if($img->saveAs(\Yii::getAlias('@webroot').$filename,0)){
            //+++++++++++++++++++++++文件本地上传成功，将文件上传至七牛云+++++++++++++++++++
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="ONQWIImIY4gjsrb530540cD7sFXR3fK4t6hPlHke";
            $secretKey = "_JiNy97QmSxfAE-jOpnEQtipQq-Q_xHF5vd9ZQMH";
            $bucket = "yangyii";
            $domain='http://p1bekxo6w.bkt.clouddn.com';//七牛云的地址
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$filename;
            // 上传到七牛后保存的文件名
            $key = $filename;
            $url="$domain/$key";//上传后七牛云的图片地址
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                //文件上传失败
                echo json_encode(['error'=>1]);
            } else {
                //文件上传成功，返回七牛云的地址
                echo json_encode(['url'=>$url]);
            }
        }
    }
    /**
     * 完成商品的列表功能
     */
    public function actionIndex(){
        //>>1.获取所有商品数据 分页
            $query=Goods::find()->where(['>','status','0']);
            //商品搜索功能
            $name=\Yii::$app->request->get('name')?\Yii::$app->request->get('name'):'';
            $sn=\Yii::$app->request->get('sn')?\Yii::$app->request->get('sn'):'';
            $minPrice=\Yii::$app->request->get('minPrice')?\Yii::$app->request->get('minPrice'):'';
            $maxPrice=\Yii::$app->request->get('maxPrice')?\Yii::$app->request->get('maxPrice'):'';
            if($name){
                //如果有搜索商品名称
                $query->andWhere(['like','name',$name]);
            }
            if($sn){
                //如果有搜索货号
                $query->andWhere(['like','sn',$sn]);
            }
            if($minPrice){
                $query->andWhere(['>','shop_price',$minPrice]);
            }
            if($maxPrice){
                $query->andWhere(['<','shop_price',$maxPrice]);
            }
            $pager=new Pagination([
                'totalCount'=>$query->count(),
                'defaultPageSize'=>2
            ]);
            $goodsList=$query->limit($pager->limit)->offset($pager->offset)->all();
            //获取所有品牌数据
            $arr=[];
            $brands=Brand::find()->select(['id','name'])->asArray()->all();
            foreach($brands as $brand){
                $arr[$brand['id']]=$brand['name'];
            }
            //获取所有的商品分类
            $array=[];
            $categorys=GoodsCategory::find()->select(['id','name'])->asArray()->all();
            foreach($categorys as $category){
                $array[$category['id']]=$category['name'];
            }

        //>>2.分配数据，显示页面
            return $this->render('index',['pager'=>$pager,'goodsList'=>$goodsList,'arr'=>$arr,'array'=>$array]);
    }

    /**
     * 完成商品的添加功能
     */
    public function actionAdd(){
        //>>1.实例化活动记录
            $model=new Goods();
            //获取所有的品牌分类
            $options=Brand::find()->select(['id','name'])->asArray()->all();
            $options=ArrayHelper::map($options,'id','name');
            //自动添加货号
             $day=date("Y-m-d",time());//当前日期
             $goodsDC=GoodsDayCount::findOne(['day'=>$day]);
             if($goodsDC){
                 //当天
                 $total=($goodsDC->count)+1;
             }else{
                 //不是当天
                 $total=1;
             }
             //货号自动补0
             $temp_num=1000000;//也可以使用str_pad 和sprintf函数方法做
             $new_num=$total+$temp_num;
             $sn=date("Ymd").substr($new_num,1,6);
             $model->sn=$sn;
        //>>2.POST方式添加数据
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //>>1.添加创建时间
                        $time=time();
                        $model->create_time=$time;
                    //>>2.将商品详情保存至goods_intro表中
                        $goodsIntro=new GoodsIntro();
                        $goodsIntro->content=$model->content;
                    //>>4.记录每天的商品添加数到dayCount表中
                        $date=date("Y-m-d",$time);//添加商品的当前时间
                         $goodsDayCount=GoodsDayCount::findOne(['day'=>$date]);//根据当前之间查找是否有与goodsDayCount表中一样的日期
                        if($goodsDayCount){//找的到数据
                            $goodsDayCount->count= ($goodsDayCount->count)+1;
                            $goodsDayCount->save();
                        }else{
                            //找不到数据
                            $goodsDayCount=new GoodsDayCount();
                            $goodsDayCount->day=$date;//创建当前日期
                            $goodsDayCount->count=1;//让当前的商品添加数等于1
                            $goodsDayCount->save();//保存数据
                        }

                    //>>5.保存数据
                        $res2=$model->save();
                        //获取新生成的id
                        $id=\Yii::$app->db->getLastInsertID();
                        $goodsIntro->goods_id=$id;
                        $res1=$goodsIntro->save();
                        $transaction=\Yii::$app->db->beginTransaction();
                        if($res1&&$res2){
                            //只有商品信息和商品详情都添加成功才提交，其他的回滚
                            $transaction->commit();
                            //发送提醒消息
                            \Yii::$app->session->setFlash('success','添加商品成功');
                            //跳转页面
                            return $this->redirect(['goods/index']);
                        }else{
                            $transaction->rollBack();
                        }




                }
            }
        //>>3.GET方式显示页面
            return $this->render('add',['model'=>$model,'options'=>$options]);
    }
    /**
     * 完成商品的修改
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=Goods::findOne(['id'=>$id]);
            //获取所有的品牌分类
            $options=Brand::find()->select(['id','name'])->asArray()->all();
            $options=ArrayHelper::map($options,'id','name');
            //获取商品详情
            $goodsIntro=GoodsIntro::findOne(['goods_id'=>$id]);
            $model->content=$goodsIntro->content;
        //>>2.POST进来保存数据
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //>>1.将商品详情修改至goods_intro表中
                        $goodsIntro->content=$model->content;
                        $res1=$goodsIntro->save();
                    //>>2.保存数据
                        $res2=$model->save();
                        $transaction=\Yii::$app->db->beginTransaction();
                        if($res1&&$res2){
                        //只有商品信息和商品详情都添加成功才提交，其他的回滚
                        $transaction->commit();
                        //发送提醒消息
                        \Yii::$app->session->setFlash('success','修改商品成功');
                        //跳转页面
                        return $this->redirect(['goods/index']);
                    }else{
                        $transaction->rollBack();
                    }




                }
            }
        //>>3.GET方式 进来显示添加表单
            return $this->render('add',['model'=>$model,'options'=>$options]);
    }
    /**
     * 完成商品的删除功能
     */
    public function actionDelete($id){
        //>>1.根据id找到这条数据并删除
            $goods=Goods::findOne(['id'=>$id]);
            $res1=$goods->delete();
            //删除该条数据的详情
            $goods_info=GoodsIntro::findOne(['goods_id'=>$id]);
            $res2=$goods_info->delete();
            //删除该条数据的相册
            $res3=GoodsGallery::deleteAll(["goods_id"=>$id]);
            $transaction=\Yii::$app->db->beginTransaction();
            if($res1&&$res2&&$res3){
                $transaction->commit();
            }else{
                $transaction->rollBack();
            }


    }
    /**
     * 完成商品的相册功能
     */
    public function actionImg($id){
        //>>1.根据id找到该条数据
            $model=Goods::findOne(['id'=>$id]);
            $gid=$id;
            //找出这些图片的相册
            $pics=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        //>>3.GET方式显示添加图片表单
            return $this->render("img",['model'=>$model,'pics'=>$pics,'gid'=>$gid]);

    }
    /**
     * 完成商品预览功能
     */
    public function actionPreview($id){
        //>>1.根据id 找到该条数据
            $model=Goods::findOne(['id'=>$id]);
        //>>2.获取该商品的内容详情
            $goodsInfo=GoodsIntro::findOne(['goods_id'=>$id]);
            $model->content=$goodsInfo->content;
        //>>3.根据id获取商品的相册图片
            $pics=GoodsGallery::find()->select(['path'])->where(['goods_id'=>$id])->all();
            $model->pics=$pics;
        //>>4.显示预览页面
            return $this->render('preview',['model'=>$model]);
    }
    /**
     * 完成相册的删除功能
     */
    public function actionDel($id,$did){
       //>>1.根据相册id找到该条数据
            $gallery=GoodsGallery::findOne(['id'=>$did]);
        //>>2.删除该条数据
            $gallery->delete();
    }
}
