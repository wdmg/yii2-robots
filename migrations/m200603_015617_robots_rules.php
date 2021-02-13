<?php

use yii\db\Migration;

/**
 * Class m200603_015617_robots_rules
 */
class m200603_015617_robots_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%robots_rules}}', [
            'id'=> $this->primaryKey(),

            'robot' => $this->string(32)->notNull(),
            'rule' => $this->string(255)->notNull(),
            'mode' => $this->integer()->defaultValue(1),
            
            'status' => $this->boolean()->defaultValue(true),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),
        ], $tableOptions);

        $this->createIndex(
            'idx_robots_rules',
            '{{%robots_rules}}',
            [
                'robot',
                'rule',
                'mode',
            ]
        );

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-robots-author}}','{{%robots_rules}}', ['created_by', 'updated_by'],false);
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_robots_to_users1',
                '{{%robots_rules}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_robots_to_users2',
                '{{%robots_rules}}',
                'updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_robots_rules', '{{%robots_rules}}');

        if (class_exists('\wdmg\users\models\Users')) {
            $this->dropIndex('{{%idx-robots-author}}', '{{%robots_rules}}');
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_robots_to_users1',
                    '{{%robots_rules}}'
                );
                $this->dropForeignKey(
                    'fk_robots_to_users2',
                    '{{%robots_rules}}'
                );
            }
        }

        $this->truncateTable('{{%robots_rules}}');
        $this->dropTable('{{%robots_rules}}');
    }

}
