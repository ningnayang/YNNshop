<?php
//命名空间
namespace backend\models;

//创建article模型类
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    //>>1.定义需要的字段
        public $content;//文章详情
    //>>2.定义验证规则
        public function rules()
        {
            return [
                //>>1.文章名，文章分类id，文章详情，状态不能为空
                [['name','article_category_id','status','content'],'required'],
                //>>2.简介可以为空
                ['intro','default','value'=>null]
            ];
        }
    //>>3.定义字段标签名称
        public function attributeLabels()
        {
            return [
                'name'=>'文章名称',
                'article_category_id'=>'文章分类',
                'status'=>'状态',
                'intro'=>'文章简介',
                'content'=>'文章详情'
            ];
        }
}
