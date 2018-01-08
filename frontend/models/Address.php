<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    //定义字段
    public $cmbProvince;
    public $cmbCity;
    public $cmbArea;
    //定义验证场景
    const SCENARIO_ADD_ADDRESS='add_address';//添加收货地址的场景
    const SCENARIO_EDIT_ADDRESS='edit_address';//修改收货地址
    //定义字段的验证规则
    public function rules()
    {
        return [
            [['username','cmbProvince','cmbCity','cmbArea','address_detail','telephone'],'required'],
            [['default','member_id','id'],'default','value'=>null],
            ['default','validateAddress','on'=>self::SCENARIO_ADD_ADDRESS],
            ['default','validateEditAddress','on'=>self::SCENARIO_EDIT_ADDRESS]
        ];
    }
    //添加地址的验证规则
    public function validateAddress(){
        $id=\Yii::$app->user->identity->id;
        $default=Address::find()->where(['default'=>1])->andWhere(['member_id'=>$id])->all();
        if($default){
            $this->addError('default','您已经有默认地址了！');
        }
    }

    //修改地址的验证规则
    public function validateEditAddress(){
        $member_id=\Yii::$app->user->identity->id;
        $id=\Yii::$app->request->get('id');
        $address=Address::findOne(['id'=>$id]);//找到这条地址
        if($address->default!=$this->default){
            $default=Address::find()->where(['default'=>1])->andWhere(['member_id'=>$member_id])->all();
            if($default){
                $this->addError('default','您已经有默认地址了！');
            }
        }


    }
}
