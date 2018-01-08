<?php
//创建命名空间
namespace backend\controllers;

//创建文章分类类
use backend\models\Article_category;
use function PHPSTORM_META\map;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class Article_categoryController extends Controller{
    /**
     * 完成文章分类的列表功能
     */
    public function actionIndex(){
        //>>1.获取所有文章分类数据
            $query=Article_category::find()->where(['>=','status',0]);
            $pager=new Pagination([
                'totalCount'=>$query->count(),//总记录数
                'defaultPageSize'=>2//每页多少条数据
            ]);
            $articleList=$query->limit($pager->limit)->offset($pager->offset)->all();
        //>>2.分配数据，显示页面
            return $this->render('index',['articleList'=>$articleList,'pager'=>$pager]);
    }
    /**
     * 完成文章的添加功能
     */
    public function actionAdd(){
        //>>1.实例化artucle活动记录
            $model=new Article_category();
        //>>2.实例化request组价
            $request=new Request();
        //>>3.post保存数据
            if($request->isPost){
                //加载数据
                $model->load($request->post());
                if($model->validate()){
                    //验证成功，保存数据
                    $model->save();
                    //提醒消息
                    \Yii::$app->session->setFlash('success','添加成功');
                    //跳转页面
                    return $this->redirect(['article_category/index']);
                }else{
                    var_dump($model->getErrors());
                }
            }
        //>>4.get显示页面
            return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成文章的修改功能
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=Article_category::findOne(['id'=>$id]);
        //>>2.POST添加数据
            $request=new Request();
            if($request->isPost){
            //加载数据
            $model->load($request->post());
            if($model->validate()){
                //验证成功，保存数据
                $model->save();
                //提醒消息
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转页面
                return $this->redirect(['article_category/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        //>>3.get回显表单
            return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成文章的删除功能
     */
    public function actionDelete($id){
        //根据id找到该条数据
        $article=Article_category::findOne(['id'=>$id]);
        //修改状态值为-1
        $article->updateAttributes(['status'=>-1]);
    }
}
