<?php
//命名空间
namespace backend\models;

//创建模型类
use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Json;

class GoodsCategory extends ActiveRecord{
    //>>1.定义字段
        public $children=[];//子孙节点
    //>>2.定义验证规则
        public function rules()
        {
            return [
                //>>1.商品名，父id不能为空
                [['name','parent_id'],'required'],
                //>>2.简介可以为空
                ['into','default','value'=>null],
                //>>3.自定义parent_id的验证规则
                ['parent_id','validateId']
            ];
        }
        //自定义验证规则
        public function validateId(){
            $parent=GoodsCategory::findOne(["id"=>$this->parent_id]);
            if(!is_object($parent)){
                return false;
            }else{
                if($parent->isChildOf($this)){
                    $this->addError('parent_id','不能够修改到自己的子孙节点下！');
                }
            }
        }


    //>>3.设置标签名字
        public function attributeLabels()
        {
            return [
                'name'=>'商品名称',
                'parent_id'=>'父分类',
                'into'=>'商品简介',
            ];
        }
    //设置排序规则

    //>>4.nest插件
        public function behaviors() {
            return [
                'tree' => [
                    'class' => NestedSetsBehavior::className(),
                    'treeAttribute' => 'tree',//必须开启才有多个根节点
                    'leftAttribute' => 'lft',
                     'rightAttribute' => 'rgt',
                    'depthAttribute' => 'depth',
                ],
            ];
        }

        public function transactions()
        {
            return [
                self::SCENARIO_DEFAULT => self::OP_ALL,
            ];
        }

        public static function find()
        {
            return new CategoryQuery(get_called_class());
        }
    //>>5.tree插件，获取所有节点分类信息
        public static function  getNodes(){
            $nodes=self::find()->select(['id','name','parent_id'])->asArray()->all();//查询对象效率低，用asArray方法提升效率
            array_unshift($nodes,['id'=>0,'name'=>'【顶层分类】','parent_id'=>'']);//添加顶层分类，让其可以添加顶层
            return Json::encode($nodes);
        }
    //>>6.获取相关父类下的子孙类
//        public function getChidren($id){
//            $childern=GoodsCategory::find()->where
//        }
}
