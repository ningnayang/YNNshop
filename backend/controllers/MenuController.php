<?php
//命名空间
namespace backend\controllers;

//创建菜单控制器类
use backend\filters\RbacFilter;
use backend\models\Menu;
use backend\models\Rbac;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller{

    /**
     * 完成菜单的列表功能
     */
    public function actionIndex(){
        //获取所有菜单数据
        $menus=Menu::find()->where(['parent_id'=>0])->all();
        $menuList=[];
        foreach($menus as $menu){
          //找到其子菜单
            $children=Menu::find()->where(['parent_id'=>$menu->id])->all();
            $menuList[]=$menu;
            if($children){
                foreach($children as $child){
                    $child->label="----".$child->label;
                    $menuList[]=$child;
                }
            }
        }
        //分配数据 显示页面
        return $this->render('index',['menuList'=>$menuList]);

    }
    /**
     * 完成菜单的添加功能
     */
    public function actionAdd(){
        //实例化菜单活动记录
        $model=new Menu();
        $model->scenario=Menu::SCENARIO_ADD_MENU;
        //获取所有菜单
        $arr=Menu::find()->select(['id','label'])->where(['parent_id'=>0])->asArray()->all();
        array_unshift($arr,['id'=>0,'label'=>'顶层分类']);
        $arr=ArrayHelper::map($arr,'id','label');
        //获取所有的路由
        $authManager=\Yii::$app->authManager;
        $routes=$authManager->getPermissions();
        $routes=ArrayHelper::map($routes,'name','name');
        //POST方式 添加数据
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                //发送提醒消息
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转页面
                return $this->redirect(['menu/index']);
            }
        }
        //GET方式显示添加列表
        return $this->render('add',['model'=>$model,'arr'=>$arr,'routes'=>$routes]);
    }
    /**
     * 完成菜单的修改功能
     */

    public function actionEdit($id){
        //找到这条数据
        $model=Menu::findOne(['id'=>$id]);
        $model->scenario=Menu::SCENARIO_EDIT_MENU;
        //获取所有菜单
        $arr=Menu::find()->select(['id','label'])->where(['parent_id'=>0])->asArray()->all();
        array_unshift($arr,['id'=>0,'label'=>'顶层分类']);
        $arr=ArrayHelper::map($arr,'id','label');
        //获取所有的路由
        $authManager=\Yii::$app->authManager;
        $routes=$authManager->getPermissions();
        $routes=ArrayHelper::map($routes,'name','name');
        //POST方式进来保存数据
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                //发送提醒消息
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转页面
                return $this->redirect(['menu/index']);
            }
        }
        //GET方式进来回显数据
        return $this->render('add',['model'=>$model,'arr'=>$arr,'routes'=>$routes]);
    }
    /**
     * 完成菜单的删除功能
     */
    public function actionDelete($id){
        //根据id找到这条数据
        $menu=Menu::findOne(['id'=>$id]);
        //删除这行
        $menu->delete();
    }
}
