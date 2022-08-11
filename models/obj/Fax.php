<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjBasic;

/**
 * This is the model class for table "fax".
 *
 * status отсутствует среди полей - удалил и здесь
 *
 * @property string $ins_ts
 * @property string|null $from
 * @property string|null $to
 * @property int $direction
 * @property string|null $type
 * @property string $typeText
 *
 */
class Fax extends ObjBasic
{
    public const DIRECTION_INCOMING = 0;
    public const DIRECTION_OUTGOING = 1;
    public const TYPE_POA_ATC = 'poa_atc';
    public const TYPE_REVOCATION_NOTICE = 'revocation_notice';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fax}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['ins_ts'], 'safe'],
                [['type'], 'required'],
                [['direction'], 'integer'],
                [['from', 'to', 'type'], 'string', 'max' => 255],
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
                'ins_ts' => Yii::t('app', 'Date'),
                'from' => Yii::t('app', 'From'),
                'to' => Yii::t('app', 'To'),
                // добавил забытые свойства
                'direction' => Yii::t('app', 'Direction'),
                'type' => Yii::t('app', 'Type'),
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTypeTexts()
    {
        return [
            self::TYPE_POA_ATC => Yii::t('app', 'POA/ATC'),
            self::TYPE_REVOCATION_NOTICE => Yii::t('app', 'Revocation'),
        ];
    }

    /**
     * @return mixed|string
     */
    public function getTypeText()
    {
        return self::getTypeTexts()[$this->type] ?? $this->type;
    }
}
