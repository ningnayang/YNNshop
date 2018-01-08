<?php
namespace frontend\models;
use yii\base\Model;

class Login extends Model{
    //>>1.定义登录表单字段
    public $username;
    public $password_hash;
   // public $code;
    public $remember;
    //>>2.定义验证规则
    public function rules(){
        return [
            //>>1.用户名和密码不能为空
            [['username','password_hash'],'required'],
            //>>2.验证码的验证规则
            //['code','captcha','captchaAction'=>'users/captcha'],
            //>>3.remember可以为空
            ['remember','default','value'=>null]
        ];
    }
    /**
     * 完成验证登录功能
     */
    public function checkLogin(){
        $member=Member::findOne(['username'=>$this->username]);
        if($member){
            //用户名存在，验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$member->password_hash)){
                //密码正确，可以登录
                //查看用户是否选择自动登录
                if($this->remember==1){
                    $duration=7*24*3600;//自动登录
                }else{
                    $duration=0;//未勾选自动登录
                }
                \Yii::$app->user->login($member,$duration);
                return true;
            }else{
                //密码不正确
                //$this->addError('password_hash','密码不正确');
                echo"密码不正确";
                return false;
            }
        }else{
            //用户名不正确
            //$this->addError('username','用户名不存在');
            echo "用户名不正确";
            return false;
        }

    }
}


