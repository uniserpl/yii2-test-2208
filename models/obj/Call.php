<?php

namespace app\models\obj;

use Yii;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%call}}".
 *
 * @property integer $direction
 * @property string $phone_from
 * @property string $phone_to
 * @property string $comment
 *
 * -- magic properties
 * @property-read string $directionText
 * @property-read string $totalStatusText
 * @property-read string $totalDisposition
 * @property-read string $durationText
 * @property-read string $fullDirectionText
 * @property-read string $client_phone
 */
class Call extends ObjCustomer
{
    const STATUS_NO_ANSWERED = 0;
    const STATUS_ANSWERED = 1;

    const DIRECTION_INCOMING = 0;
    const DIRECTION_OUTGOING = 1;

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
                [['direction', 'phone_from', 'phone_to', 'type', 'viewed'], 'required'],
                [['direction', 'type'], 'integer'],
                [['phone_from', 'phone_to', 'outcome'], 'string', 'max' => 255],
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
                'direction' => Yii::t('app', 'Direction'),
                'directionText' => Yii::t('app', 'Direction'),
                'phone_from' => Yii::t('app', 'Caller Phone'),
                'phone_to' => Yii::t('app', 'Dialed Phone'),
            ]
        );
    }


    /**
     * @return string
     */
    public function getClient_phone()
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
            } else 
            if ($this->direction == self::DIRECTION_OUTGOING) {
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
     * @return type
     */
    public static function getStatusTexts() {
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
