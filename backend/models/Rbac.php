<?php
namespace backend\models;

use yii\base\Model;

class Rbac extends Model{
    //>>1.定义字段
        public $name;
        public $description;
        //定义场景
        const SCENARIO_ADD_PERMISSION='add-permission';//添加权限场景
        const SCENARIO_EDIT_PERMISSION='edit-permission';//修改权限场景
    //>>2.验证规则
        public function rules()
        {
            return [
                //权限名和描述不能为空
                [['name','description'],'required'],
                //权限名的验证规则，不能重复,添加场景
                ['name','validateName','on'=>self::SCENARIO_ADD_PERMISSION],
                //权限名验证规则，除自身之外，不能重复，修改场景
                ['name','valiName','on'=>self::SCENARIO_EDIT_PERMISSION]
            ];
        }
        //自定义验证规则，名称不能重复添加
        public function validateName(){
            //取出已有的权限名称
            $authManager=\Yii::$app->authManager;
            $perms=$authManager->getPermission($this->name);
            if($perms){
                $this->addError('name','该权限已存在!');
            }

        }
        //自定义验证规则，修改
        public function valiName(){
            //>>1.获取之前的名称
                $old_name=\Yii::$app->request->get('name');
            //>>2.判断
                $authManager=\Yii::$app->authManager;
                if($old_name!==$this->name){
                    $perms=$authManager->getPermission($old_name);
                    if($perms){
                        $this->addError('name','该权限名称已经存在');
                    }
                }

        }
    //>>2.标签名称
        public function attributeLabels()
        {
            return [
               'name'=>'权限名称(路由)',
                'description'=>'描述'
            ];
        }
}
