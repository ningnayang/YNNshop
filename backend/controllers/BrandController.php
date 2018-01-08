<?php
//创建命名空间
namespace  backend\controllers;

//创建品牌控制器类
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class BrandController extends Controller{
public $enableCsrfValidation=false;//关闭验证

    /**
     * 完成品牌的列表显示功能
     */
    public function actionIndex(){
        //>>1.获取所有品牌数据 分页
            $query=Brand::find()->where(['>=','status',0]);
            $pager=new Pagination([
                'totalCount'=>$query->count(),//总记录数
                'defaultPageSize'=>2//每页多少条数据
            ]);
            $brandList=$query->limit($pager->limit)->offset($pager->offset)->all();
        //>.2.分配数据，显示页面
            return $this->render('index',['brandList'=>$brandList,'pager'=>$pager]);
    }
    /**
     * 完成品牌的添加功能
     */
    public function actionAdd(){
        //>>1.实例化品牌活动记录
            $model=new Brand();
        //>>2.实例化request组件
            $request=new Request();
        //>>3.post方式进来添加数据
            if($request->isPost){
                //加载数据
                $model->load($request->post());
                //验证成功保存数据
                if($model->validate()){
                   //如果没有图片给一张默认图片
                    if(!$model->logo){
                        $model->logo='http://p1bekxo6w.bkt.clouddn.com//upload/5a3bd54f0589d.jpg';
                    }
                    //保存数据
                    $model->save();
                    //发送提示信息
                    \Yii::$app->session->setFlash('success','添加品牌成功');
                    //跳转页面
                    return $this->redirect(['brand/index']);
                }else{
                    //验证不成功,打印错误信息
                    var_dump($model->getErrors());
                }
            }
        //>>4.GET方式进来显示添加表单
            return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成品牌的修改功能
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=Brand::findOne(['id'=>$id]);
        //>>2.POST方式保存修改数据
            $request=new Request();
            if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证成功保存数据
            if($model->validate()){
                //保存数据
                $model->save();
                //发送提示信息
                \Yii::$app->session->setFlash('success','修改品牌成功');
                //跳转页面
                return $this->redirect(['brand/index']);
            }else{
                //验证不成功,打印错误信息
                var_dump($model->getErrors());
            }
        }
        //>>3.GET方式进来回显
            return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成品牌的删除功能
     */
    public function actionDelete($id){
        //>>1.根据id找到该条数据
            $brand=Brand::findOne(['id'=>$id]);
        //>>2.将该条数据的状态修改为-1删除
            $brand->updateAttributes(['status'=>-1]);

    }
    /**
     * 完成处理图片上传功能
     */
    public function actionUpload(){
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

}
