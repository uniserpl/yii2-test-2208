<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjCustomer;

/**
 * This is the model class for table "{{%sms}}".
 *
 * Свойства type не существует, удаляем из списка
 * 
 * phone_to и direction в правилах required, а в БД м.б. null, следует синхронизировать
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
    const DIRECTION_INCOMING = 0;
    const DIRECTION_OUTGOING = 1;

    // incoming
    const STATUS_NEW = 0;
    const STATUS_READ = 1;
    const STATUS_ANSWERED = 2;

    // outgoing
    const STATUS_DRAFT = 10;
    const STATUS_WAIT = 11;
    const STATUS_SENT = 12;
    const STATUS_DELIVERED = 13;
    const STATUS_FAILED = 14;
    const STATUS_SUCCESS = 13;


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
                // удаляем из правил несуществующие в БД поля: 'applicant_id', 'type'
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
