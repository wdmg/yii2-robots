<?php

namespace wdmg\robots\models;

use wdmg\helpers\ArrayHelper;
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
    const RULE_MODE_CLEAN = 2;
    const RULE_MODE_HOST = 3;
    const RULE_MODE_DELAY = 4;
    const RULE_MODE_SITEMAP = 5;

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
            [['robot'], 'string', 'min' => 1, 'max' => 32],
            [['rule'], 'string', 'min' => 0, 'max' => 255],
            [['mode'], 'integer'],
            [['rule', 'mode'], 'checkRuleSyntax'],
            [['status'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users')) {
            $rules[] = [['created_by', 'updated_by'], 'required'];
        }

        return $rules;
    }

    /**
     * Custom rule syntax validation.
     *
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkRuleSyntax($attribute, $params) {
        $modes = $this->getModesList(false);
        if ((intval($this->mode) == self::RULE_MODE_HOST) || (intval($this->mode) == self::RULE_MODE_SITEMAP)) {
            $validator = new \yii\validators\UrlValidator();
            if (!$validator->validate($this->rule)) {
                $this->addError('rule', Yii::t('app/modules/robots', 'The value of the field `{attribute}` must be a URL provided that `{mode}`.', [
                    'attribute' => 'rule',
                    'mode' => $modes[intval($this->mode)]
                ]));
                return false;
            }
        } elseif (intval($this->mode) == self::RULE_MODE_DELAY) {
            $validator = new \yii\validators\NumberValidator();
            if (!$validator->validate($this->rule)) {
                $this->addError('rule', Yii::t('app/modules/robots', 'The value of the field `{attribute}` must be an integer provided that `{mode}`.', [
                    'attribute' => 'rule',
                    'mode' => $modes[intval($this->mode)]
                ]));
                return false;
            }
        } elseif (intval($this->mode) == self::RULE_MODE_CLEAN) {
            $validator = new \yii\validators\RegularExpressionValidator([
                'pattern' => '/[A-Za-z0-9\-\_\=\?\&\%\/\.\$\~\* ]/u'
            ]);
            if (!$validator->validate($this->rule)) {
                $this->addError('rule', Yii::t('app/modules/robots', 'The value of the field `{attribute}` can contain only Latin letters, numbers and spaces, as well as wildcards: -, _, =,?, &,%, /,., $, ~, * provided that `{mode} `.', [
                    'attribute' => 'rule',
                    'mode' => $modes[intval($this->mode)]
                ]));
                return false;
            }
        } else {
            $validator = new \yii\validators\RegularExpressionValidator([
                'pattern' => '/[A-Za-z0-9\-\_\=\?\&\%\/\.\$\~\*]/u'
            ]);
            if (!$validator->validate($this->rule)) {
                $this->addError('rule', Yii::t('app/modules/robots', 'The value of the field `{attribute}` can contain only Latin letters and numbers, as well as wildcards: -, _, =,?, &,%, /,., $, ~, * provided that `{mode}`.', [
                    'attribute' => 'rule',
                    'mode' => $modes[intval($this->mode)]
                ]));
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/robots', 'ID'),
            'robot' => Yii::t('app/modules/robots', 'Robot'),
            'rule' => Yii::t('app/modules/robots', 'Rule path or value'),
            'mode' => Yii::t('app/modules/robots', 'Mode'),
            'status' => Yii::t('app/modules/robots', 'Status'),
            'created_at' => Yii::t('app/modules/robots', 'Created at'),
            'created_by' => Yii::t('app/modules/robots', 'Created by'),
            'updated_at' => Yii::t('app/modules/robots', 'Updated at'),
            'updated_by' => Yii::t('app/modules/robots', 'Updated by'),
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

    /**
     * Return all published rules for `robots.txt` grouped by `robots`.
     *
     * @param bool $asArray
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getPublished($asArray = false) {
        $query = self::find()->where(['status' => self::RULE_STATUS_ACTIVE])
            ->groupBy(['robot', 'rule'])
            ->orderBy(['mode' => SORT_ASC, 'id' => SORT_DESC]);

        if ($asArray)
            $query->asArray();

        return $query->all();
    }

    /**
     * Return all robots list.
     *
     * @param bool $allRobots
     * @return array
     */
    public static function getRobotsList($allRobots = false) {
        $list = [];
        if ($allRobots) {
            $list = [
                '*' => Yii::t('app/modules/robots', 'All robots')
            ];
        }

        $robots = self::find()->select('robot', 'DISTINCT')->groupBy('robot')->asArray()->all();
        return ArrayHelper::merge($list, ArrayHelper::map($robots, 'robot', 'robot'));
    }

    /**
     * Return statuses list.
     *
     * @param bool $allStatuses
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        $list = [];
        if ($allStatuses) {
            $list = [
                '*' => Yii::t('app/modules/robots', 'All statuses')
            ];
        }

        $list = \yii\helpers\ArrayHelper::merge($list, [
            self::RULE_STATUS_DISABLED => Yii::t('app/modules/robots', 'Disabled'),
            self::RULE_STATUS_ACTIVE => Yii::t('app/modules/robots', 'Active'),
        ]);

        return $list;
    }

    /**
     * Return modes list.
     *
     * @param bool $allModes
     * @return array
     */
    public function getModesList($allModes = false)
    {
        $list = [];
        if ($allModes) {
            $list = [
                '*' => Yii::t('app/modules/robots', 'All modes')
            ];
        }

        $list = \yii\helpers\ArrayHelper::merge($list, [
            self::RULE_MODE_ALLOW => Yii::t('app/modules/robots', 'Allow'),
            self::RULE_MODE_DISALLOW => Yii::t('app/modules/robots', 'Disallow'),
            self::RULE_MODE_CLEAN => Yii::t('app/modules/robots', 'Clean-Param'),
            self::RULE_MODE_HOST => Yii::t('app/modules/robots', 'Host'),
            self::RULE_MODE_DELAY => Yii::t('app/modules/robots', 'Crawl-delay'),
            self::RULE_MODE_SITEMAP => Yii::t('app/modules/robots', 'Sitemap'),
        ]);

        return $list;
    }
}
