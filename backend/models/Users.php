<?php
//命名空间
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface {
    //设置验证密码需要的字段
    public $rePassword;//确认密码
    public $oldPassword;//旧密码
    public $newPassword;//新密码
    public $roles;//给用户添加权限
    //>>1.设置字段验证规则
        public function rules(){
            return [
                //>>1.用户名和密码和状态不能为空
                    [['username','password','status'],'required'],
                //>>2.邮箱可以为空
                     [['email','auth_key','roles'],'default','value'=>null],
                //>>3.旧密码可以为空
                    [['oldPassword'],'default','value'=>null],
                //>>4.确认密码的验证规则
                    [['rePassword','oldPassword','newPassword'],'validateRe']
            ];
        }
        //自定义密码验证规则
        public function validateRe(){
            if($this->oldPassword){
                //填写了旧密码,则修改密码
                if(!$this->newPassword){
                    //没填新密码
                    $this->addError('newPassword','新密码不能为空');
                }elseif(!$this->rePassword){
                   //没填确认密码
                    $this->addError('rePassword','确认密码不能为空');
                }elseif($this->newPassword&&$this->rePassword){
                    //填写了新密码和确认密码，两次密码要一致
                    if($this->newPassword!==$this->rePassword){
                        $this->addError('rePassword','新密码和确认密码不一致');
                    }else{
                        //如果新密码和确认密码一致，验证旧密码是否填写正确
                        $res=\Yii::$app->security->validatePassword($this->oldPassword,$this->password);
                        if(!$res){
                            $this->addError('oldPassword','旧密码填写错误');
                        }
                    }
                }
            }else{
                //没有填写旧密码，则不修改密码
            }
        }
    //>>2.设置字段标签
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'status'=>'状态',
            'email'=>'邮箱',
            'rePassword'=>'确认密码',
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'roles'=>'添加角色'
        ];
    }
    //>>3.获取该用户菜单
        public function getMenus(){
            $menuItems=[];
            //获取所有一级菜单
            $menus=Menu::find()->where(['parent_id'=>0])->all();
            foreach($menus as $menu){
                //获取所有一级菜单的子菜单
                $children=Menu::find()->where(['parent_id'=>$menu->id])->all();
                $items=[];
                //构造格式
                foreach($children as $child){
                    //判断用户是否有该菜单的权限
                    //if(\Yii::$app->user->can($child->url)){
                        $items[]=['label'=>$child->label,'url'=>[$child->url]];//注意格式
                   // }
                }
                //构造格式
                //没有子菜单的一级菜单不需要显示
               // if($items){
                    $menuItems[]=['label'=>$menu->label,'items'=>$items];
               // }
            }
            return $menuItems;

        }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
       return  $this->getAuthKey()==$authKey;

}
}
