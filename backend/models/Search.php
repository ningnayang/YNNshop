<?php
//命名空间
namespace backend\models;

//创建搜索类
use yii\base\Model;

class Search extends Model{
    //>>1.搜索需要定义的字段
        public $name;
        public $sn;
        public $minPrice;
        public $maxPrice;
    //>>2.定义所有验证规则
        public function rules()
        {
            return [
                //以上皆可以为空
                [['name','sn','minPrice','maxPrice'],'default','value'=>null]
            ];
        }
    //>>3.定义标签名称
        public function attributeLabels()
        {
            return [
               'name'=>'商品名称',
                'sn'=>'货号',
                'minPrice'=>'最低价格',
                'maxPrice'=>'最高价格'
            ];
        }
}