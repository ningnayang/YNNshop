<?php

use yii\db\Migration;

class m180103_112955_alter_address_table extends Migration
{
    public function up()
    {
        $this->dropColumn('address','member_id');
        $this->addColumn('address','username','string');
    }

    public function down()
    {
        echo "m180103_112955_alter_address_table cannot be reverted.\n";

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
