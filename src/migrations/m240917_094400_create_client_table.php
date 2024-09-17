<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m240917_094400_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'cpf' => $this->string(14)->notNull()->unique(),
            'gender' => $this->string(1)->notNull(), // Use 'M' or 'F' as per model rules
            'name' => $this->string(50)->notNull(),
            'cep' => $this->string(9)->notNull(),
            'street' => $this->string(255)->notNull(),
            'number' => $this->string(10)->notNull(),
            'city' => $this->string(255)->notNull(),
            'state' => $this->string(2)->notNull(),
            'complement' => $this->string(255)->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client}}'); // Fixed the table name
    }
}
