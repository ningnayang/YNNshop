<?php
//命名空间
namespace backend\controllers;

//创建文章类
use backend\models\Article;
use backend\models\Article_category;
use backend\models\Article_detail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
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
     * 完成文章的列表功能
     */
    public function actionIndex(){
        //>>1.获取所有文章数据,分页
            $model=new Article();
            $query=Article::find()->where(['>=','status',0]);
            $pager=new Pagination([
               'totalCount'=>$query->count(),//总记录数
                'defaultPageSize'=>2//每页多少条数据
            ]);
            $articleList=$query->limit($pager->limit)->offset($pager->offset)->all();
            //获取所有的文章分类
            $arr=[];
            $categorys=Article_category::find()->select(['id','name'])->asArray()->all();
            foreach($categorys as $category){
                $arr[$category['id']]=$category['name'];
            }
        //>>>2.分配数据，显示页面
            return $this->render('index',['model'=>$model,'articleList'=>$articleList,'pager'=>$pager,'arr'=>$arr]);
    }
    /**
     * 完成文章的添加功能
     */
    public function actionAdd(){
        //>>1.实例化活动记录 request组件
            $model=new Article();
            $request=new Request();
            //获取所有的文章分类数据
            $category=Article_category::find()->select(['id','name'])->all();
            $options=ArrayHelper::map($category,'id','name');
        //>>2.post方式进来添加数据
            if($request->isPost){
                //加载数据
                $model->load($request->post());
                if($model->validate()){
                    //将文章详情保存至article_detail表
                    $article_detail=new Article_detail();
                    $model->create_time=time();
                    $article_detail->content=$model->content;
                    $article_detail->save();//保存
                    $model->save();//保存文章基本信息数据至article表
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','添加成功');
                    //跳转页面
                    return $this->redirect(['article/index']);
                }
            }
        //>>3.get方式进来显示添加列表
            return $this->render('add',['model'=>$model,'options'=>$options]);

    }
    /*
     * 完成文章的修改功能
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=Article::findOne(['id'=>$id]);
            $article_detail=Article_detail::findOne(['article_id'=>$id]);
            $model->content=$article_detail->content;
            //获取所有的文章分类数据
            $category=Article_category::find()->select(['id','name'])->all();
            $options=ArrayHelper::map($category,'id','name');
        //>>2.post方式进来保存数据
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //将文章内容修改至article_detail表
                    $article_detail->updateAttributes(['content'=>$model->content]);
                    $model->save();//保存数据
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','修改成功');
                    //跳转页面
                    return $this->redirect(['article/index']);
                }
            }
        //>>3.get方式进来回显数据
            return $this->render('add',['model'=>$model,'options'=>$options]);

    }
    /**
     * 完成文章的删除功能
     */
    public function actionDelete($id){
        //>>1.根据id找到当前数据
            $article=Article::findOne(['id'=>$id]);
        //>>2.将当前的状态值改为-1
            $article->updateAttributes(['status'=>-1]);
    }
}
