<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180103_110928_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户'),
            'address'=>$this->string(50)->comment('所在地区'),
            'address_detail'=>$this->string(100)->comment('详细地址'),
            'telephone'=>$this->integer(13)->comment('手机号码'),
            'default'=>$this->integer(1)->comment('设置为默认地址')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
