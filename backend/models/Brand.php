<?php
//创建命名空间
namespace backend\models;

//创建品牌模型类
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    //>>1.定义其他需要的字段
        public $imgFile;//删除图片logo
    //>>2.定义字段的验证规则
        public function rules()
        {
            return [
                //>>1.名字、状态、排序不能为空
                    [['name','status'],'required'],
                //>>2.简介,排序可以为空
                    [['intro','sort','logo'],'default','value'=>null],
                //>>3.上传文件的验证规则
                    //['imgFile','file','extensions'=>['jpg','png','gif'],'maxSize'=>1024*1024,'skipOnEmpty'=>true]

            ];
        }
    //>>3.定义字段的标签名字
        public function attributeLabels()
        {
            return [
                'name'=>'品牌名称',
                'status'=>'状态',
                'sort'=>'排序',
                'intro'=>'简介',
                'logo'=>'上传图片'
            ];
        }
}
