<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use app\components\DynObjBehavior;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property integer $id
 * @property string $ins_ts
 * @property integer $customer_id
 * @property integer $user_id
 * @property string $event
 * @property string $object
 * @property integer $object_id
 * @property string $message
 * @property string $detail
 *
 * @property-read string $eventText
 *
 * @property-read Customer $customer
 * @property-read User $user
 *
 * @property-read obj\Task $objTask
 * @property-read obj\Sms  $objSms
 * @property-read obj\Call $objCall
 * @property-read obj\Fax  $objFax
 * @property-read ObjBasic $obj
 * @property-read string $objName
 * @property-read ObjBasic $objModel
 */
class History extends ActiveRecord
{
    public const EVENT_CREATED_TASK = 'created_task';
    public const EVENT_UPDATED_TASK = 'updated_task';
    public const EVENT_COMPLETED_TASK = 'completed_task';

    public const EVENT_INCOMING_SMS = 'incoming_sms';
    public const EVENT_OUTGOING_SMS = 'outgoing_sms';

    public const EVENT_INCOMING_CALL = 'incoming_call';
    public const EVENT_OUTGOING_CALL = 'outgoing_call';

    public const EVENT_INCOMING_FAX = 'incoming_fax';
    public const EVENT_OUTGOING_FAX = 'outgoing_fax';

    public const EVENT_CUSTOMER_CHANGE_TYPE = 'customer_change_type';
    public const EVENT_CUSTOMER_CHANGE_QUALITY = 'customer_change_quality';

    /**
     * Карта событий и связанных с ними текстовых ресурсов
     *
     * @var string[]
     */
    private static $_events = [
        self::EVENT_CREATED_TASK => 'Task created',
        self::EVENT_UPDATED_TASK => 'Task updated',
        self::EVENT_COMPLETED_TASK => 'Task completed',

        self::EVENT_INCOMING_SMS => 'Incoming message',
        self::EVENT_OUTGOING_SMS => 'Outgoing message',

        self::EVENT_OUTGOING_CALL => 'Outgoing call',
        self::EVENT_INCOMING_CALL => 'Incoming call',

        self::EVENT_INCOMING_FAX => 'Incoming fax',
        self::EVENT_OUTGOING_FAX => 'Outgoing fax',

        self::EVENT_CUSTOMER_CHANGE_TYPE => 'Type changed',
        self::EVENT_CUSTOMER_CHANGE_QUALITY => 'Property changed',
    ];

    /**
     * Карта зависимости объекта от события
     *
     * Используется при выводе ленты
     *
     * @var string[]
     */
    private static $_objects = [
        self::EVENT_CREATED_TASK => 'objTask',
        self::EVENT_UPDATED_TASK => 'objTask',
        self::EVENT_COMPLETED_TASK => 'objTask',

        self::EVENT_INCOMING_SMS => 'objSms',
        self::EVENT_OUTGOING_SMS => 'objSms',

        self::EVENT_OUTGOING_CALL => 'objCall',
        self::EVENT_INCOMING_CALL => 'objCall',

        self::EVENT_INCOMING_FAX => 'objFax',
        self::EVENT_OUTGOING_FAX => 'objFax',

        self::EVENT_CUSTOMER_CHANGE_TYPE => false,
        self::EVENT_CUSTOMER_CHANGE_QUALITY => false,
    ];

    /**
     * Кеш переведенных текстовых ресурсов для событий
     *
     * @var string[]
     */
    private static $_eventsLabel = [];

    /**
     * Декодированное поле detail
     *
     * @var \stdClass|null
     */
    private $_details;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ins_ts'], 'safe'],
            [['customer_id', 'object_id', 'user_id'], 'integer'],
            [['event'], 'required'],
            [['message', 'detail'], 'string'],
            [['event', 'object'], 'string', 'max' => 255],
            [
                ['customer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Customer::class,
                'targetAttribute' => ['customer_id' => 'id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'DynObjBehavior' => [
                /**
                 * Поведение обеспечивает доступ к объектам на лету:
                 * objCall, objFax, objSms, objTask, ...
                 */
                'class' => DynObjBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterRefresh()
    {
        $this->_details = null;
        parent::afterRefresh();
    }

    /**
     * Метки атрибутов
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ins_ts' => Yii::t('app', 'Ins Ts'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'event' => Yii::t('app', 'Event'),
            'object' => Yii::t('app', 'Object'),
            'object_id' => Yii::t('app', 'Object ID'),
            'message' => Yii::t('app', 'Message'),
            'detail' => Yii::t('app', 'Detail'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Возвращает связанный объект независимо от его соответствия событию
     *      т.е. если событие EVENT_CREATED_TASK, а object==sms
     *      то $this->obj вернёт Sms() а не Task()
     * 
     * @return ActiveQuery
     */
    public function getObj()
    {
        $object = $this->getAttribute('object');
        if (empty($object) || empty($this->object_id)) {
            return null;
        }
        $class = DynObjBehavior::NS_OBJECT_CLASS . ucfirst($object);
        if (class_exists($class)) {
            return $this->hasOne($class, ['id' => 'object_id']);
        }
        return null;
    }

    /**
     * Карта всех событий
     *
     * @return array
     */
    public static function getEventTexts()
    {
        $map = & self::$_eventsLabel;
        array_walk(self::$_events, function ($label, $event) use (&$map) {
            // create cache only if nedded
            if (!array_key_exists($event, $map)) {
                $map[$event] = Yii::t('app', $label);
            }
        });

        return self::$_eventsLabel;
    }

    /**
     * Кеширует и возвращает переведенное название для события
     *
     * @param string $event
     * @return string
     */
    public static function getEventTextByEvent($event)
    {
        if (array_key_exists($event, self::$_eventsLabel)) {
            // read cache
            return self::$_eventsLabel[$event];
        } elseif (array_key_exists($event, self::$_events)) {
            // create cache
            return self::$_eventsLabel[$event] = Yii::t('app', self::$_events[$event]);
        } else {
            return $event;
        }
    }

    /**
     * Возвращает переведенное название для события
     *
     * @return string
     */
    public function getEventText()
    {
        return static::getEventTextByEvent($this->event);
    }


    /**
     * Возвращает атрибут из поля detail
     *
     * @param string $attribute
     */
    private function _detail($attribute)
    {
        if (is_null($this->_details)) {
            // make cache
            $this->_details = json_decode($this->detail);
        }
        return isset($this->_details->{$attribute}) ? $this->_details->{$attribute} : null;
    }

    /**
     * @param string $attribute
     * @return mixed|null
     */
    public function getDetailChangedAttribute($attribute)
    {
        $chng = $this->_detail('changedAttributes');
        return isset($chng->{$attribute}) ? $chng->{$attribute} : null;
    }

    /**
     * @param string $attribute
     * @return mixed|null
     */
    public function getDetailOldValue($attribute)
    {
        $attr = $this->getDetailChangedAttribute($attribute);
        return isset($attr->old) ? $attr->old : null;
    }

    /**
     * @param string $attribute
     * @return mixed|null
     */
    public function getDetailNewValue($attribute)
    {
        $attr = $this->getDetailChangedAttribute($attribute);
        return isset($attr->new) ? $attr->new : null;
    }

    /**
     * @param string $attribute
     * @return mixed|null
     */
    public function getDetailData($attribute)
    {
        $data = $this->_detail('data');
        return isset($data->{$attribute}) ? $data->{$attribute} : null;
    }

    public static function getObjectNameByEvent($event)
    {
        return isset(self::$_objects[$event]) ? self::$_objects[$event] : null;
    }

    public function getObjName($default = null)
    {
        $objName = self::getObjectNameByEvent($this->event);
        return $objName ? $objName : $default;
    }

    public function getObjModel()
    {
        $objName = $this->getObjName();
        return $objName ? $this->$objName : $this->obj;
    }
}
