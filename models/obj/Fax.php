<?php

namespace app\models\obj;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ObjBasic;

/**
 * This is the model class for table "fax".
 *
 * @property string $from
 * @property string $to
 * @property integer $status
 * @property integer $direction
 * @property integer $type
 * @property string $typeText
 *
 */
class Fax extends ObjBasic {

    const DIRECTION_INCOMING = 0;
    const DIRECTION_OUTGOING = 1;
    const TYPE_POA_ATC = 'poa_atc';
    const TYPE_REVOCATION_NOTICE = 'revocation_notice';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%fax}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['type'], 'required'],
                [['from', 'to'], 'string'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'from' => Yii::t('app', 'From'),
                'to' => Yii::t('app', 'To')
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTypeTexts() {
        return [
            self::TYPE_POA_ATC => Yii::t('app', 'POA/ATC'),
            self::TYPE_REVOCATION_NOTICE => Yii::t('app', 'Revocation'),
        ];
    }

    /**
     * @return mixed|string
     */
    public function getTypeText() {
        return self::getTypeTexts()[$this->type] ?? $this->type;
    }

}
