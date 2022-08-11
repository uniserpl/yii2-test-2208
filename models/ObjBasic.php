<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Суперкласс любого объекта (history.object)
 *    
 * @see Fax
 * 
 * ObjBasic также расширяется с помощью класса ObjCustomer
 * 
 * @property integer $id
 * @property string $ins_ts
 * @property integer $user_id
 *
 * @property User $user
 */
abstract class ObjBasic extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id'], 'integer'],
            [['user_id'], 'required'],
            [['ins_ts'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ins_ts' => Yii::t('app', 'Date'),
            'user_id' => Yii::t('app', 'User ID'),
            'user.fullname' => Yii::t('app', 'User'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
