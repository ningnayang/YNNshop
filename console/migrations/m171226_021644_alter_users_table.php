<?php

use yii\db\Migration;

class m171226_021644_alter_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('users','auth_key','string');
    }

    public function down()
    {
        echo "m171226_021644_alter_users_table cannot be reverted.\n";

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
