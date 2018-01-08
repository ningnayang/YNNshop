<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171230_061806_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'label'=>$this->string('30')->comment('菜单名'),
            'url'=>$this->string('30')->comment('路由'),
            'parent_id'=>$this->integer()->comment('上级菜单'),
            'sort'=>$this->integer()->comment('排序')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
