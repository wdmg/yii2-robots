<?php

namespace wdmg\robots\models;

use Yii;

/**
 * This is the model class for table "{{%robots_rules}}".
 *
 * @property int $id
 *
 * @property string $robot
 * @property string $rule
 * @property int $mode
 *
 * @property int $status
 *
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Users $user
 */

class Rules extends \yii\db\ActiveRecord
{

    const RULE_STATUS_DISABLED = 0;
    const RULE_STATUS_ACTIVE = 1;

    const RULE_MODE_DISALLOW = 0;
    const RULE_MODE_ALLOW = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%robots_rules}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['robot', 'rule', 'mode'], 'required'],
            [['robot'], 'string', 'max' => 32],
            [['rule'], 'string', 'max' => 255],
            [['mode'], 'integer'],
            [['status'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users')) {
            $rules[] = [['created_by', 'updated_by'], 'required'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/votes', 'ID'),
            'robot' => Yii::t('app/modules/votes', 'Robot'),
            'rule' => Yii::t('app/modules/votes', 'Rule'),
            'mode' => Yii::t('app/modules/votes', 'Mode'),
            'status' => Yii::t('app/modules/votes', 'Status'),
            'created_at' => Yii::t('app/modules/votes', 'Created at'),
            'created_by' => Yii::t('app/modules/votes', 'Created by'),
            'updated_at' => Yii::t('app/modules/votes', 'Updated at'),
            'updated_by' => Yii::t('app/modules/votes', 'Updated by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return null;
    }
}
