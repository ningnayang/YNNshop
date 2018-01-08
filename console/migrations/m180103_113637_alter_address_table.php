<?php

use yii\db\Migration;

class m180103_113637_alter_address_table extends Migration
{
    public function up()
    {
        $this->addColumn('address','member_id','integer');
    }

    public function down()
    {
        echo "m180103_113637_alter_address_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
