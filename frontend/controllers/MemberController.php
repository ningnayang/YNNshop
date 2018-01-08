<?php
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Login;
use frontend\models\Member;
use MongoDB\BSON\Regex;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use frontend\models\SignatureHelper;

class MemberController extends Controller{
    public $enableCsrfValidation=false;

    /**
     * 完成用户的注册功能
     */
    public function actionRegister(){
        //以POST方式进来保存数据
        $request=new Request();
        if($request->isPost){
            $model=new Member();
            $model->load($request->post(),'');
            if($model->validate()){
                //将密码存hash值
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                //生成一个随机字符串，存入auth_key中
                $str="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $string=str_shuffle($str);
                $auth_key=substr($string,-7);//截取7个字符串
                $model->auth_key=$auth_key;
                //保存创建时间
                $model->created_at=time();
                //保存值
                $model->save(false);
                //跳转页面
                return $this->redirect(['member/login']);
            }

        }
        return $this->render('register');
    }

    /**
     * 完成验证用户名的功能
     */
    public function actionValidateName($username){
        //查询该用户名是否已经注册
        $memberName=Member::findOne(['username'=>$username]);
        if($memberName){
            //已经注册
            echo 'false';
        }else{
            echo 'true';
        }
    }
    /**
     * 完成用户登录的功能
     */
    public function actionLogin(){
        //post进来完成验证登录功能
        $request=new Request();
        if($request->isPost){
            $model=new Login();
            $model->load($request->post(),'');
            if($model->checkLogin()){
                //如果验证成功,保存当前登录时间和登录ip
                $time=time();
                $member=Member::findOne(['id'=>\Yii::$app->user->identity->id]);
                $member->updateAttributes(['last_login_time'=>$time]);
                $ip=\Yii::$app->request->userIP;
                $member->updateAttributes(['last_login_ip'=>$ip]);
                $member->save(false);
                //跳转至网站首页
                return $this->redirect(['site/index']);
            }
        }
        return $this->render('login');
    }
    /**
     * 完成用户注销功能
     */
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','退出登录成功');
        return $this->redirect(['member/login']);
    }
    /**
     * 完成用户的收货地址功能
     */
    public function actionAddress(){
        //以GET方式进来显示表单
        //获取该用户的收货地址
        if(\Yii::$app->user->isGuest){
            //没有登录用户进入收货地址让其登录
            return $this->redirect(['member/login']);
        }
        $id=\Yii::$app->user->identity->id;
        $addrs=Address::find()->where(['member_id'=>$id])->orderBy('default desc,id asc')->all();
        foreach($addrs as $addr){
           $array=explode(',',$addr->address);
           $addr->cmbProvince=$array[0];
           $addr->cmbCity=$array[1];
           $addr->cmbArea=$array[2];
        }
        //以POST方式进来完成地址的添加功能
        $request=new Request();
        if($request->isPost){
            $model=new Address();
            $model->scenario=Address::SCENARIO_ADD_ADDRESS;
            $model->load($request->post(),'');
            if($model->validate()){
                $arr=[$model->cmbProvince,$model->cmbCity,$model->cmbArea];
                $address=implode(',',$arr);
                $model->address=$address;
                $model->save();
                //跳转回地址页
                return $this->redirect(['member/address']);
            }
        }
        return $this->render('address',['addrs'=>$addrs]);
    }
    /**
     * 完成修改收货地址的功能
     */
    public function actionEditAddress($id){
       //GET方式进来显示修改表单
        //用户的所有收货地址
        $address=Address::findOne(['id'=>$id]);
        $address->scenario=Address::SCENARIO_EDIT_ADDRESS;
        $addr=explode(",",$address->address);
        $address->cmbProvince=$addr[0];
        $address->cmbCity=$addr[1];
        $address->cmbArea=$addr[2];
        //POST方式进来完成修改的保存
        $request=new Request();
        if($request->isPost){
            $address->load($request->post(),'');
            if($address->validate()){
                $arr=[$address->cmbProvince,$address->cmbCity,$address->cmbArea];
                $add=implode(',',$arr);
                $address->address=$add;
                $address->save();
                //跳转页面
                return $this->redirect(['member/address']);
            }
        }
        return $this->render('editAddr',['address'=>$address]);
    }
    /**
     * 完成删除收货地址的功能
     */
    public function actionDeleteAddress($id){
        //>>1.找到该条数据
        $address=Address::findOne(['id'=>$id]);
        $address->delete();


    }

    /**
     * 完成设置默认地址和取消默认地址的功能
     */
    public function actionDefault($id,$memberId){
        //>>将状态值改为1
        $address=Address::findOne(['id'=>$id]);
        if($address->default!=1){
            //设置为默认地址
            //查找是否已经有默认地址
            $default=Address::find()->where(['default'=>1])->andWhere(['member_id'=>$memberId])->all();
            if($default){
                return $this->redirect(['member/address']);
            }
            $address->updateAttributes(['default'=>1]);
        }else{
            //取消默认地址
            $address->updateAttributes(['default'=>0]);
        }
        return $this->redirect(['member/address']);
    }
    /**
     * 检验该用户下是否有默认地址添加
     */
    public function actionCheckDefault(){
        $id=\Yii::$app->user->identity->id;
        $default=Address::find()->where(['default'=>1])->andWhere(['member_id'=>$id])->all();
        if($default){
            echo Json::encode('false');
        }else{
            echo Json::encode('true');
        }
    }
    /**
     * 该用户下是否有默认地址
     */
    public function actionEditDefault($id){
        $member_id=\Yii::$app->user->identity->id;
        $default=Address::find()->where(['default'=>1])->andWhere(['member_id'=>$member_id])->andWhere(['!=','id',$id])->all();
        if($default){
            echo Json::encode('false');
        }else{
            echo Json::encode('true');
        }
    }
    /**
     * 测试阿里大于短信功能
     */
    public function actionSms($phone){
        //发送短息前先对电话号码进行验证,正则表达式验证电话号码
        if(!preg_match("/^1[34578]{1}\d{9}$/",$phone)){
           return "号码格式不正确";
        }
        $code=rand(1000,9999);
        $result=\Yii::$app->sms->send($phone,['code'=>$code]);
        if($result->Code=="OK"){
            //短信发送成功
            //将验证码保存在redis中
            $redis=new \Redis();
            $redis->connect('127.0.0.1');
            $redis->set("code_".$phone,$code,30*60);
            return 'true';
        }else{
            //短信发送失败
            return '短息发送失败';
        }

       /* $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIrjxqFN0lpR45";
        $accessKeySecret = "lyMTykRqixkbCd2ti10hCGW7xBBW1C";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = "15982232209";

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "杨记小铺";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_120115281";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => rand(1000,9999),
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        // $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );

        var_dump($content) ;*/
    }
    /**
     * 验证验证码的功能
     */
    public function actionValidateCaptcha($tel){
        //从redis中获取验证码
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $result=$redis->get("code_".$tel);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

}
