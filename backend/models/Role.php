<?php
namespace backend\models;
use yii\base\Model;

class Role extends Model{
    ////>>1.定义字段
        public $name;
        public $description;
        public $permissions;
        //定义场景
        const SCENARIO_ADD_ROLE='add_role';//添加角色的场景
        const SCENARIO_EDIT_ROLE='edit_role';//修改角色场景
    //>>2.验证规则
        public function rules()
        {
            return [
                //角色名和描述不能为空
                [['name','description'],'required'],
                //权限可以为空
                ['permissions','default','value'=>null],
                //角色名的验证规则
                ['name','validateName','on'=>self::SCENARIO_ADD_ROLE],
                //角色名验证规则，修改
                ['name','validateEditName','on'=>self::SCENARIO_EDIT_ROLE]
            ];
        }
        //添加角色的验证规则
        public function validateName(){
            $authManager=\Yii::$app->authManager;
            $role=$authManager->getRole($this->name);
            if($role){
                $this->addError('name','该角色名称已经存在');
            }
        }
        //角色验证规则，修改
        public function validateEditName(){
            $old_name=\Yii::$app->request->get('name');
            if($old_name!==$this->name){
                    $authManager=\Yii::$app->authManager;
                    $role=$authManager->getRole($this->name);
                    if($role){
                        $this->addError('name','该角色名称已经存在');
                    }
                }
            }

    //>>2.标签名称
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'描述',
            'permissions'=>'添加权限'
        ];
    }
}
