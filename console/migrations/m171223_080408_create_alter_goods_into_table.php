<?php

use yii\db\Migration;

/**
 * Handles the creation of table `alter_goods_into`.
 */
class m171223_080408_create_alter_goods_into_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
       $this->dropColumn('goods_intro','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('alter_goods_into');
    }
}
