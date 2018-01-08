<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    public $address_id;
    public $delivery;
    public $pay;
    public $goodsPics=[];//商品图片
    //定义快递方式
    public static  $deliveries=[
        1=>['顺风快递',25,'速度像风一样快，服务优质'],
        2=>['EMS',20,'傲娇的服务'],
        3=>['圆通',10,'申通是我哥'],
        4=>['申通',10,'圆通是我弟']
        ];
    //定义付款方式
    public static $pays=[
        1=>['货到付款',"送货上门后再收款，支持现金、POS机刷卡、支票支付"],
        2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        3=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账']
    ];
    //定义字段的验证规则
    public function rules()
    {
        return [
            //不能为空
            [['address_id','delivery','pay'],'required'],
        ];
    }

}
