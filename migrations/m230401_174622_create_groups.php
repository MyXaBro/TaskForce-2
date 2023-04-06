<?php

use yii\db\Migration;

/**
 * Class m230401_174622_create_groups
 */
class m230401_174622_create_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%groups}}',[
            'id'=> $this->primaryKey(),
            'name'=> $this->char()->notNull(),
            'parent_id' => $this-> integer()
        ]);

        $this -> addColumn('users', 'group_id', $this -> integer());

        $this -> addForeignKey('user_group', 'users', 'group_id','groups', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230401_174622_create_groups cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230401_174622_create_groups cannot be reverted.\n";

        return false;
    }
    */
}
