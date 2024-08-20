<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notify}}`.
 */
class m240820_100856_create_notify_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notify}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(),
            'author_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notify}}');
    }
}
