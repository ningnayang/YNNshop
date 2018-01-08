<?php
//命名空间
namespace backend\models;

//菜单模型类
use yii\db\ActiveRecord;

class Menu extends ActiveRecord{
    //定义验证场景
    const SCENARIO_ADD_MENU='add-menu';
    const SCENARIO_EDIT_MENU = 'edit-menu';
    //字段的验证规则
    public function rules()
    {
        return [
            //名称，上级菜单，路由，排序不能为空
            [['label','url','parent_id','sort'],'required'],
            //名称唯一
            ['label','unique'],
            //菜单不能重复添加
            ['url','validateUrl','on'=>self::SCENARIO_ADD_MENU],
            //菜单修改除自己外不能重复添加
            ['url','validateEUrl','on'=>self::SCENARIO_EDIT_MENU]
        ];
    }
    //自定义验证规则
    public function validateUrl(){
        $menu=Menu::find()->where(['url'=>$this->url])->one();
        if($menu){
            $this->addError('url','该路由已经存在');
        }
    }

    public function validateEUrl(){
        $id=\Yii::$app->request->get('id');
        $menu=Menu::findOne(['id'=>$id]);
        $old=$menu->url;
        if($this->url!==$old){
            $menu=Menu::find()->where(['url'=>$this->url])->one();
            if($menu){
                $this->addError('url','该路由已经存在');
            }
        }
    }

    //设置标签名称
    public function attributeLabels()
    {
        return [
            'label'=>'名称',
            'url'=>'路由',
            'parent_id'=>'上级菜单',
            'sort'=>'排序'
        ];
    }
}
