<?php
namespace backend\controllers;

//创建用户控制器类
use backend\filters\RbacFilter;
use backend\models\Login;
use backend\models\Rbac;
use backend\models\Role;
use backend\models\Users;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class UsersController extends  Controller{
    /**
     * 设置验证码
     */
        public function actions(){
            return [
                'captcha'=>[
                    'class'=>CaptchaAction::className(),
                    //验证码设置
                    'minLength'=>3,
                    'maxLength'=>4,
                    'padding'=>0
                ]
            ];
        }
    /**
     * 过滤器配置权限
     */
   /* public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }*/
    /**
     * 完成登录功能
     */
    public function actionLogin(){
        //>>1.实例化登录模型
            $model=new Login();
        //>>2.如果以POST方式进来则进行验证
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->checkLogin()){
                    //如果验证成功,保存当前登录时间和登录ip
                    $time=time();
                    $user=Users::findOne(['id'=>\Yii::$app->user->identity->id]);
                    $user->updateAttributes(['last_login_time'=>$time]);
                    $ip=\Yii::$app->request->userIP;
                    $user->updateAttributes(['last_login_ip'=>$ip]);
                    $user->save(false);
                    //发送提醒消息
                    \Yii::$app->session->setFlash('success','登录成功');
                    //跳转至管理员列表页
                    return $this->redirect(['users/index']);
                }
            }
        //>>3.GET方式进来显示登录页面
            return $this->render('login',['model'=>$model]);
    }
    /**
     * 完成退出登录功能
     */
    public function actionLogout(){
        //>>1.退出登录
            \Yii::$app->user->logout();
            \Yii::$app->session->setFlash('success','退出登录成功');
            return $this->redirect(['users/login']);
    }
    /**
     * 完成用户的列表功能
     */
    public function actionIndex(){
        //>>1.获取所有管理员数据，分页
            $query=Users::find();
            $pager=new Pagination([
                'totalCount'=>$query->count(),
                'defaultPageSize'=>10
            ]);
            $userList=$query->limit($pager->limit)->offset($pager->offset)->all();
        //>>2.分配数据 显示页面
            return $this->render('index',['userList'=>$userList,'pager'=>$pager]);
    }
    /**
     * 完成用户的添加功能
     */
    public function actionAdd(){
        //>>1.实例化用户模型
            $model=new Users();
            //获取所有角色
            $authManager=\Yii::$app->authManager;
            $roles=$authManager->getRoles();
            $options=ArrayHelper::map($roles,'name','description');
        //>>2.post 添加
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $model->password=\Yii::$app->security->generatePasswordHash($model->password);
                    //生成一个随机字符串，存入auth_key中
                    $str="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                    $string=str_shuffle($str);
                    $auth_key=substr($string,-7);//截取7个字符串
                    $model->auth_key=$auth_key;
                    $model->save();
                    $id=\Yii::$app->db->lastInsertID;
                    //给用户添加权限
                    if($model->roles){
                        foreach($model->roles as $role){
                            $role=$authManager->getRole($role);
                            $authManager->assign($role,$id);
                        }
                    }
                    //提醒消息
                    \Yii::$app->session->setFlash('success','添加用户成功');
                    //跳转页面
                    return $this->redirect(['users/index']);
                }
            }
        //>>3.GET 显示添加页面
            return $this->render('add',['model'=>$model,'options'=>$options]);
    }
    /**
     * 完成管理员的修改功能
     */
    public function actionEdit($id){
        //>>1.根据id找到该条数据
            $model=Users::findOne(['id'=>$id]);
            $authManager=\Yii::$app->authManager;
            $roles=$authManager->getRoles();
            $options=ArrayHelper::map($roles,'name','description');
            $arr=[];
            $userRoles=$authManager->getRolesByUser($id);
            foreach($userRoles as $role){
              $arr[]=$role->name;
            }
            $model->roles=$arr;
        //>>2.POST保存数据
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $model->password=\Yii::$app->security->generatePasswordHash($model->password);
                    $model->save();
                    //给用户添加角色
                    //清除所有角色
                    $authManager->revokeAll($id);
                    //然后添加角色
                    if($model->roles){
                        foreach($model->roles as $role){
                            $role=$authManager->getRole($role);
                            $authManager->assign($role,$id);
                        }
                    }
                    //提醒消息
                    \Yii::$app->session->setFlash('success','修改用户成功');
                    //跳转页面
                    return $this->redirect(['users/index']);
                }
            }
        //>>3.GET显示表单
            return $this->render('add',['model'=>$model,'options'=>$options]);

    }
    /**
     * 完成管理员的删除功能
     */
    public function actionDelete($id){
        //>>1.根据id找到这条数据
            $user=Users::findOne(['id'=>$id]);
            $user->delete();
    }
    /**
     * 完成修改自己密码的功能
     */
    public function actionPassword(){
        //>>1.根据id找到用户数据
            $id=\Yii::$app->user->identity->id;
            $model=Users::findOne(['id'=>$id]);
        //>>2.POST方式进来完成修改密码的功能
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    if($model->oldPassword){
                        //填写了旧密码 修改密码
                        $model->updateAttributes(['password'=>\Yii::$app->security->generatePasswordHash($model->rePassword)]);
                        \Yii::$app->session->setFlash('success','修改密码成功');
                    }
                    return $this->redirect(['users/index']);
                }
            }
        //>>3..GET方式 进来显示修改表单
            return $this->render('password',['model'=>$model]);
    }
}
