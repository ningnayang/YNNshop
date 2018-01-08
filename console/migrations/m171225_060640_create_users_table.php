<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m171225_060640_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username'=>$this->string("50")->comment('用户名'),
            'password'=>$this->string("150")->comment('密码'),
            'email'=>$this->string("50")->comment('邮箱'),
            'status'=>$this->integer(1)->comment('状态'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
