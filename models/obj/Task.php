<?php

namespace app\models\obj;

use Yii;
use yii\db\ActiveQuery;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property string $title
 * @property string $text
 * @property string $due_date
 * @property integer $priority
 * @property string $ins_ts
 *
 * @property string $stateText
 * @property string $state
 * @property string $subTitle
 *
 * @property boolean $isOverdue
 * @property boolean $isDone
 *
 * @property string $isInbox
 * @property string $statusText
 */
class Task extends ObjCustomer
{
    const STATUS_NEW = 0;
    const STATUS_DONE = 1;
    const STATUS_CANCEL = 3;

    const STATE_INBOX  = 'inbox';
    const STATE_DONE   = 'done';
    const STATE_FUTURE = 'future';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['title'], 'required'],
                [['priority'], 'integer'],
                [['text'], 'string'],
                [['title', 'object'], 'string', 'max' => 255],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'title' => Yii::t('app', 'Title'),
                'text' => Yii::t('app', 'Description'),
                'due_date' => Yii::t('app', 'Due Date'),
                'priority' => Yii::t('app', 'Priority'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function getStatusTexts()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'New'),
            self::STATUS_DONE => Yii::t('app', 'Complete'),
            self::STATUS_CANCEL => Yii::t('app', 'Cancel'),
        ];
    }


    /**
     * @return array
     */
    public static function getStateTexts()
    {
        return [
            self::STATE_INBOX => Yii::t('app', 'Inbox'),
            self::STATE_DONE => Yii::t('app', 'Done'),
            self::STATE_FUTURE => Yii::t('app', 'Future')
        ];
    }

    /**
     * @return mixed
     */
    public function getStateText()
    {
        return self::getStateTexts()[$this->state] ?? $this->state;
    }


    /**
     * @return bool
     */
    public function getIsOverdue()
    {
        return $this->status !== self::STATUS_DONE && strtotime($this->due_date) < time();
    }

    /**
     * @return bool
     */
    public function getIsDone()
    {
        return $this->status == self::STATUS_DONE;
    }
}
