<?php
//创建命名空间
namespace backend\models;

//创建文章分类模型
use yii\db\ActiveRecord;

class Article_category extends ActiveRecord{
    //>>1.定义字段
    //>>2.定义验证规则
        public function rules()
        {
            return [
                //>>1.文章名和状态不能为空
                    [['name','status'],'required'],
                //>>2.简介和排名可以为空
                    [['intro','sort'],'default','value'=>null]
            ];
        }
    //>>3.定义标签名称
        public function attributeLabels()
        {
            return [
                'name'=>'文章名称',
                'intro'=>'简介',
                'status'=>'状态'
            ];
        }
}
