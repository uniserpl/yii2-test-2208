<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property int $user_id
 * @property string $title
 * @property string|null $text
 * @property string|null $due_date
 * @property int|null $priority
 *
 * @property string $title
 * @property string $text
 * @property string $due_date
 * @property integer $priority
 * @property string $ins_ts
 *
 * @property-read boolean $isOverdue
 * @property-read boolean $isDone
 *
 * Т.к. нет поля state удаляем всё, что с ним связано
 * включая константы и методы getStateTexts() getStateText()
 * Так же не существует свойств subTitle и isInbox, удаляем из описания класса
 */
class Task extends ObjCustomer
{
    public const STATUS_NEW = 0;
    public const STATUS_DONE = 1;
    public const STATUS_CANCEL = 3;

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
                // Добавляем забытые поля status и due_date
                // Убираем лишнее поле object
                [['title'], 'required'],
                [['status', 'priority'], 'integer'],
                [['text'], 'string'],
                [['due_date'], 'safe'],
                [['title'], 'string', 'max' => 255],
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
