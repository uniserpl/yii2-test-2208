<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%sms}}".
 *
 * @property string|null $phone_from
 * @property string $phone_to
 * @property string|null $message
 * @property int $direction
 *
 * @property-read string $directionText
 */
class Sms extends ObjCustomer
{
    public const DIRECTION_INCOMING = 0;
    public const DIRECTION_OUTGOING = 1;

    // incoming
    public const STATUS_NEW = 0;
    public const STATUS_READ = 1;
    public const STATUS_ANSWERED = 2;

    // outgoing
    public const STATUS_DRAFT = 10;
    public const STATUS_WAIT = 11;
    public const STATUS_SENT = 12;
    public const STATUS_DELIVERED = 13;
    public const STATUS_FAILED = 14;
    public const STATUS_SUCCESS = 13;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['phone_to', 'direction'], 'required'],
                [['direction'], 'integer'],
                [['message'], 'string'],
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
            parent::attributeLabels(),
            [
                'phone_from' => Yii::t('app', 'Phone From'),
                'phone_to' => Yii::t('app', 'Phone To'),
                'message' => Yii::t('app', 'Message'),
                'direction' => Yii::t('app', 'Direction'),
                'directionText' => Yii::t('app', 'Direction'),
            ]
        );
    }

    /**
     * @return array
     */
    public static function getStatusTexts()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'New'),
            self::STATUS_READ => Yii::t('app', 'Read'),
            self::STATUS_ANSWERED => Yii::t('app', 'Answered'),

            self::STATUS_DRAFT => Yii::t('app', 'Draft'),
            self::STATUS_WAIT => Yii::t('app', 'Wait'),
            self::STATUS_SENT => Yii::t('app', 'Sent'),
            self::STATUS_DELIVERED => Yii::t('app', 'Delivered'),
            self::STATUS_FAILED => Yii::t('app', 'Failed'),
            self::STATUS_SUCCESS => Yii::t('app', 'Success'),
        ];
    }

    /**
     * @return array
     */
    public static function getDirectionTexts()
    {
        return [
            self::DIRECTION_INCOMING => Yii::t('app', 'Incoming'),
            self::DIRECTION_OUTGOING => Yii::t('app', 'Outgoing'),
        ];
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function getDirectionTextByValue($value)
    {
        return self::getDirectionTexts()[$value] ?? $value;
    }

    /**
     * @return mixed|string
     */
    public function getDirectionText()
    {
        return self::getDirectionTextByValue($this->direction);
    }
}
