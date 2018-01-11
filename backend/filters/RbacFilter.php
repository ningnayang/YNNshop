<?php
//命名空间
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{
    //在操作执行之前
    public function beforeAction($action)
    {
        if(!\Yii::$app->user->can($action->uniqueId)){//如果用户没有权限
            //如果没有登录，引导用户登录
            if(\Yii::$app->user->isGuest){
                //跳转至登录页面
                //注意，一定要加send,因为即使这个用户有权限，但是没有登录，如果不加登录也相当于return true
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();//模型没有跳转方法，只有控制器才有，所有要先找到这个控制器
            }
            //没有权限 抛出异常
            throw new HttpException(403,'对不起，您没有该操作权限');
        }
        return true;
    }
}
