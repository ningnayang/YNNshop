<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord{
    public $logo;
    public $price;
    //定义字段验证规则
    public function rules()
    {
        return [
         [['amount','goods_id','member_id'],'required']
        ];
    }
}
