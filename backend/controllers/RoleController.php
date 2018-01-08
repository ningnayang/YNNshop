<?php
namespace backend\controllers;

use backend\models\Rbac;
use backend\models\Role;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class RoleController extends Controller{
    /**
     * 完成角色的列表功能
     */
    public function actionIndex(){
        //获取所有的角色数据
        $authManager=\Yii::$app->authManager;
        $roles=$authManager->getRoles();
        //分配数据，显示页面
        return $this->render('index',['roles'=>$roles]);
    }
    /**
     * 完成角色的添加功能
     */
    public function actionAdd(){
        $model=new Role();
        //验证指定场景
          $model->scenario=Role::SCENARIO_ADD_ROLE;
        //获取所有权限
         $authManager=\Yii::$app->authManager;
         $permissions=$authManager->getPermissions();
         $options=ArrayHelper::map($permissions,'name','description');
        //>>1.POST添加角色
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //创建角色
                    $role=new \yii\rbac\Role();
                    $role->name=$model->name;
                    $role->description=$model->description;
                    //保存角色
                    $authManager->add($role);
                    //给角色添加权限
                    if($model->permissions){
                        foreach($model->permissions as $permission){
                            //获取权限对象
                            $permi=$authManager->getPermission($permission);
                            $authManager->addChild($role,$permi);
                        }
                    }
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','添加成功');
                    //跳转页面
                    return $this->redirect(['role/index']);
                }
            }
        //>>2.GET显示添加表单
        return $this->render('add',['model'=>$model,'options'=>$options]);
    }
    /**
     * 完成角色的修改功能
     */
    public function actionEdit($name){
        $authManager=\Yii::$app->authManager;
        $role=$authManager->getRole($name);
        $old=$role->name;
        $model=new Role();
        //指定验证场景
        $model->scenario=Role::SCENARIO_EDIT_ROLE;
        $model->name=$role->name;
        $model->description=$role->description;
        $permissions=$authManager->getPermissions();
        $options=ArrayHelper::map($permissions,'name','description');
        //获取角色的权限
        $pers=$authManager->getPermissionsByRole($old);
        $arr=[];
        foreach($pers as $per){
            $arr[]=$per->name;
        }
        $model->permissions=$arr;
        //POST添加数据
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //修改角色
                $authManager->removeChildren($role);//取消角色的所有权限
                $role->name=$model->name;
                $role->description=$model->description;
                //保存修改角色
                $authManager->update($old,$role);
                //给角色添加权限
                if($model->permissions){
                    foreach($model->permissions as $permission){
                        //获取权限对象
                        $permi=$authManager->getPermission($permission);
                        $authManager->addChild($role,$permi);
                    }
                }
                //发送提醒消息
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转页面
                return $this->redirect(['role/index']);
            }
        }
        //GET显示表单
        return $this->render('add',['model'=>$model,'options'=>$options]);
    }
    /**
     * 完成角色的删除功能
     */
    public function actionDelete($name){
        //>>1.根据名称找到该角色
            $authManager=\Yii::$app->authManager;
            $role=$authManager->getRole($name);
        //>>2.删除该角色
            $authManager->remove($role);
    }
}
