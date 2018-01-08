<?php
//创建命名空间
namespace backend\models;

//创建商品模型类
use yii\db\ActiveRecord;
use yii\helpers\Json;

class Goods extends ActiveRecord{
    //>>1.需要定义的字段
        public $content;
        public $pics;//相册图片
    //>>2.字段的验证规则
        public function rules()
        {
            return [
                //>>1.商品名称，货号，图片，品牌分类，市场价格，商品价格，库存，是否在售，状态，排序，不能为空
                [['name','sn','logo','brand_id','market_price','shop_price','stock','is_on_sale','status','sort','goods_category_id'],'required'],
                //>>2.商品详情可以为空
                ['content','default','value'=>null]
            ];
        }

    //>>3.字段的标签名字
        public function attributeLabels()
        {
            return [
                'name'=>'商品名称',
                'sn'=>'货号',
                'logo'=>'上传图片',
                'goods_category_id'=>'商品分类',
                'brand_id'=>'品牌分类',
                'market_price'=>'市场价格',
                'shop_price'=>'商品价格',
                'stock'=>'库存',
                'is_on_sale'=>'是否在售',
                'status'=>'状态',
                'sort'=>'排序',
                'content'=>'商品详情',
            ];
        }
    
}
