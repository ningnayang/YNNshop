<?php
//命名空间
namespace backend\controllers;

//创建商品类 控制器类
use backend\models\Goods;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\data\Sort;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;

class GoodsCategoryController extends Controller{
    public $enableCsrfValidation=false;
    /**
     * 完成商品的列表功能
     */
    public function actionIndex(){
        //>>1.获取所有数据 分页
            $query=GoodsCategory::find();
//            $pager=new Pagination([
//                'totalCount'=>$query->count(),
//                'defaultPageSize'=>2//默认每页2条数据
//            ]);
            //设置排序规则
            $categoryList=$query->orderBy('tree asc,lft asc')->all();
            foreach($categoryList as &$category){
               $category['name']=str_repeat("----",$category['depth']).$category['name'];
            }
        //>>2.分配数据 显示页面
            return $this->render('index',['categoryList'=>$categoryList]);
    }
    /**
     * 完成添加功能
     */
    public function actionAdd(){
        //>>1.实例化活动记录和request组件
            $request=new Request();
            $model=new GoodsCategory();
        //>>2.POST进来提交数据
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //处理节点
                    if($model->parent_id){
                        //创建子节点
                        $parent=GoodsCategory::findOne(["id"=>$model->parent_id]);
                        $model->appendTo($parent);
                    }else{
                        //创建父节点
                        $model->makeRoot();
                    }
                    //清除redis
                    $redis=new \Redis();
                    $redis->connect('127.0.0.1');
                    $redis->del('categorys');
                    //保存数据m
                    $model->save();
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','添加成功');
                    //返回首页
                    return $this->redirect(['index']);
                }
            }
        //>>3.GET进来显示页面
            return $this->render('add',['model'=>$model]);
    }

    /**
     * 测试tree插件
     */
    public function actionTree(){
        return $this->renderPartial('tree');//无须bootstrap效果，使用layout=false 或者renderPartial
    }

    /**
     * 完成商品表的修改功能
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=GoodsCategory::findOne(['id'=>$id]);
            //$parent_id=$model->parent_id;
        //>>2.POST方式提交表单
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //处理节点
                    if($model->parent_id){
                        //创建子节点,必须重新创建，直接保存左右值不会变
                        $parent=GoodsCategory::findOne(["id"=>$model->parent_id]);
                        $model->appendTo($parent);
                    }else{
                        //创建父节点
                        //根节点修改为根节点报错，这是ztree的bug,怎么规避，做判断，如果以前的父id不为0
                        if($model->getOldAttribute('parent_id')){
                            $model->makeRoot();
                        }else{
                            ;$model->save();
                        }
                    }
                    //清除redis
                    $redis=new \Redis();
                    $redis->connect('127.0.0.1');
                    $redis->del('categorys');
                //保存数据m
               // $model->save();
                //发送提醒消息
                \Yii::$app->session->setFlash('success','修改成功');
                //返回首页
                return $this->redirect(['index']);
            }
        }
        //>>3.GET方式进来显示修改表单
            return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成商品分类的删除功能
     */
    public function actionDelete($id){
        //>>1.根据id找到该条数据;
            $category=GoodsCategory::findOne(['id'=>$id]);
            //获取该节点的
            //查询是否有子节点
            $nodes=GoodsCategory::find()->where(['parent_id'=>$id])->all();
            //查询该分类下是否有商品存在
            $goods=Goods::find()->where(['goods_category_id'=>$id])->all();
        //>>2.删除该条数据
            if($nodes&&$goods){
                //如果有子节点和商品
                echo Json::encode(1);
            }elseif(!$nodes&&$goods){
                //没有子节点，有商品
                echo Json::encode(2);
            }elseif(!$goods&&$nodes){
                //有子节点，没有商品
                echo Json::encode(3);
            }else{
                //没有子节点和商品
                //如果是根节点
                if($category->parent_id!=0){
                    $category->delete();
                }else{
                    $category->deleteWithChildren();
                }

                echo Json::encode(4);
            }
        //>>3.清除redis
        //清除redis
            $redis=new \Redis();
            $redis->connect('127.0.0.1');
            $redis->del('categorys');


    }
}


