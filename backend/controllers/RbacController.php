<?php
namespace backend\controllers;

use backend\models\Rbac;
use yii\rbac\Permission;
use yii\web\Controller;
use yii\web\Request;

class RbacController extends Controller{
    /**
     * 完成权限的列表功能
     */
    public function actionIndex(){
        $authManager=\Yii::$app->authManager;
        //获取所有权限
        $permissions=$authManager->getPermissions();
        //分配数据显示页面
        return $this->render('index',['permissions'=>$permissions]);
    }
    /**
     * 完成权限的添加功能
     */
    public function actionAdd(){
        $model=new Rbac();
        $model->scenario=Rbac::SCENARIO_ADD_PERMISSION;//指定验证场景
        $authManager=\Yii::$app->authManager;
        //post方式添加权限
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //创建一个新的权限
                $permission=new Permission();
                //给权限赋值
                $permission->name=$model->name;
                $permission->description=$model->description;
                //添加权限
                $authManager->add($permission);
                \Yii::$app->session->setFlash('success','添加权限成功');
                //跳转页面
                return $this->redirect(['rbac/index']);
            }
        }
        //get 进来显示添加页面
        return $this->render('add',['model'=>$model]);
    }
    /**
     * 完成权限的修改功能
     */
    public function actionEdit($name){
            $authManager=\Yii::$app->authManager;
        //>>1.找到该权限
            $permission=$authManager->getPermission($name);
            $old=$permission->name;
            $model=new Rbac();
            $model->name=$permission->name;
            $model->description=$permission->description;
            $model->scenario=Rbac::SCENARIO_EDIT_PERMISSION;
        //>>2.以POST方式 保存
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $permission->name=$model->name;
                    $permission->description=$model->description;
                    $authManager->update($old,$permission);
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['rbac/index']);
                }
            }
            return $this->render('add',['model'=>$model]);

    }
    /**
     * 完成权限的删除功能
     */
    public function actionDelete($name){
        $authManager=\Yii::$app->authManager;
        $permission=$authManager->getPermission($name);
        $authManager->remove($permission);
    }
}
