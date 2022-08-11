<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%call}}".
 *
 * @property int $direction
 * @property string $ins_ts
 * @property string|null $comment
 *
 * следующие свойства в БД м.б. null, следует синхронизировать с rules
 * @property string $phone_from
 * @property string $phone_to
 *
 * @property-read string $clientPhone
 * @property-read string $durationText
 * @property-read string $fullDirectionText
 * @property-read string $totalDisposition
 * @property-read string $totalStatusText
 *
 * directionText - не существующее свойство, удалил из property-read
 */
class Call extends ObjCustomer
{
    public const STATUS_NO_ANSWERED = 0;
    public const STATUS_ANSWERED = 1;

    public const DIRECTION_INCOMING = 0;
    public const DIRECTION_OUTGOING = 1;

    public $duration = 720;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%call}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // 'type', 'viewed', 'outcome' - не могут упоминаться в правилах
                //  т.к. не существуют в БД и в модели
                // 'comment' - наоборот, забыли упомянуть
                [['ins_ts'], 'safe'],
                [['direction', 'phone_from', 'phone_to'], 'required'],
                [['direction'], 'integer'],
                [['comment'], 'string'],
                [['phone_from', 'phone_to'], 'string', 'max' => 255],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                'ins_ts' => Yii::t('app', 'Date'),
                'direction' => Yii::t('app', 'Direction'),
                'phone_from' => Yii::t('app', 'Caller Phone'),
                'phone_to' => Yii::t('app', 'Dialed Phone'),
                'directionText' => Yii::t('app', 'Direction'),

                // 'comment' и здесь забыли добавить
                'comment' => Yii::t('app', 'Comment'),
            ]
        );
    }


    /**
     * Исправил название ради camel caps format
     * @return string
     */
    public function getClientPhone()
    {
        return $this->direction == self::DIRECTION_INCOMING ? $this->phone_from : $this->phone_to;
    }

    /**
     * @return string
     */
    public function getDurationText()
    {
        // Переворачиваем условие, улучшаем читабельность
        if (empty($this->duration)) {
            return '00:00';
        }
        return gmdate($this->duration >= 3600 ? "H:i:s" : "i:s", $this->duration);
    }

    /**
     * @return mixed|string
     */
    public function getTotalStatusText()
    {
        // Упрощаем логику, чтобы легче читался код
        if ($this->status == self::STATUS_NO_ANSWERED) {
            if ($this->direction == self::DIRECTION_INCOMING) {
                return Yii::t('app', 'Missed Call');
            } elseif ($this->direction == self::DIRECTION_OUTGOING) {
                return Yii::t('app', 'Client No Answer');
            }
        }

        $direction = $this->getFullDirectionText();

        if (empty($this->duration)) {
            return $direction;
        }

        return $direction . ' (' . $this->getDurationText() . ')';
    }

    /**
     * Заглушка для абстрактного метода
     *
     * вместо getStatusText() в модели используется getTotalStatusText()
     *
     * @return type
     */
    public static function getStatusTexts()
    {
        return [];
    }

    /**
     * @param bool $hasComment
     * @return string
     */
    public function getTotalDisposition($hasComment = true)
    {
        // Упрощаем логику
        return $hasComment && $this->comment ? $this->comment : '';
    }

    /**
     * @return array
     */
    public static function getFullDirectionTexts()
    {
        return [
            self::DIRECTION_INCOMING => Yii::t('app', 'Incoming Call'),
            self::DIRECTION_OUTGOING => Yii::t('app', 'Outgoing Call'),
        ];
    }

    /**
     * @return mixed|string
     */
    public function getFullDirectionText()
    {
        return self::getFullDirectionTexts()[$this->direction] ?? $this->direction;
    }
}
