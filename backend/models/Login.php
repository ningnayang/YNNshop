<?php
namespace backend\models;

//创建登录模型类

use yii\base\Model;

class Login extends Model{
    //>>1.定义登录表单字段
        public $username;
        public $password;
        public $code;
        public $remember;
    //>>2.定义验证规则
    public function rules(){
        return [
            //>>1.用户名和密码不能为空
                [['username','password'],'required'],
            //>>2.验证码的验证规则
                ['code','captcha','captchaAction'=>'users/captcha'],
            //>>3.remember可以为空
                ['remember','default','value'=>null]
        ];
    }
    //>.3.定义标签字段
    public function attributeLabels()
    {
        return[
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'remember'=>'记住我'
        ];
    }
    //>>4.定义验证登录的方法
    public function checkLogin(){
        $user=Users::findOne(['username'=>$this->username]);
        if($user){
            //用户名存在，验证密码
            if(\Yii::$app->security->validatePassword($this->password,$user->password)){
                //密码正确，可以登录
                //查看用户是否选择自动登录
                if($this->remember==1){
                    $duration=7*24*3600;//自动登录
                }else{
                    $duration=0;//未勾选自动登录
                }
                \Yii::$app->user->login($user,$duration);
                return true;
            }else{
                //密码不正确
                $this->addError('password','密码不正确');
                return false;
            }
        }else{
            //用户名不正确
            $this->addError('username','用户名不存在');
            return false;
        }

    }

}