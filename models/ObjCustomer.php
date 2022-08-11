<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * Суперкласс любого объекта (history.object) содержащего кастомера и статус
 *
 * @see Sms, Task, Call
 *
 * @property int $status
 * @property int $customer_id
 *
 * @property-read string $statusText
 * @property-read Customer $customer
 */
abstract class ObjCustomer extends ObjBasic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['customer_id', 'status'], 'required'],
                [['customer_id', 'status'], 'integer'],
                [
                    ['customer_id'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => Customer::class,
                    'targetAttribute' => ['customer_id' => 'id']
                ],
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
                'status' => Yii::t('app', 'Status'),
                'statusText' => Yii::t('app', 'Status'),
                'customer_id' => Yii::t('app', 'Customer ID'),
                'customer.name' => Yii::t('app', 'Client'),
            ]
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     *
     * @return string[]
     */
    abstract public static function getStatusTexts();

    /**
     * @param int $value
     * @return string
     */
    public function getStatusTextByValue($value)
    {
        return self::getStatusTexts()[$value] ?? $value;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return self::getStatusTextByValue($this->status);
    }
}
