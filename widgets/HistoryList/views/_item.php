<?php

use app\models\History;
use app\models\Customer;
use app\models\obj\Call;
use app\models\obj\Sms;
use yii\helpers\Html;

// Использование хелпера переехало в _item_common

/* @var $model \app\models\search\HistorySearch */

switch ($model->event) {
    case History::EVENT_CREATED_TASK:
    case History::EVENT_COMPLETED_TASK:
    case History::EVENT_UPDATED_TASK:
        /* @var $task \app\models\obj\Task */
        $task = $model->objTask;

        echo $this->render('_item_common', [
            // Убрал повторяющуюся передачу параметров, зависящих от $model
            // Вместо этого, всё нужное извлекается сразу в шаблоне _item_common
            'history' => $model,
            'iconClass' => 'fa-check-square bg-yellow',
            'footer' => isset($task->customerCreditor->name) ? "Creditor: " . $task->customerCreditor->name : ''
        ]);
        break;

    case History::EVENT_INCOMING_SMS:
    case History::EVENT_OUTGOING_SMS:
        /* @var $task \app\models\obj\Sms */
        $sms = $model->objSms;
        echo $this->render('_item_common', [
            'history' => $model,
            'footer' => empty($sms) ? '' : ($sms->direction == Sms::DIRECTION_INCOMING
                ? Yii::t('app', 'Incoming message from {number}', ['number' => $sms->phone_from ?? ''])
                : Yii::t('app', 'Sent message to {number}', ['number' => $sms->phone_to ?? ''])),
            // iconIncome - не используется в шаблоне _item_common, удаляем
            'iconClass' => 'icon-sms bg-dark-blue'
        ]);
        break;

    case History::EVENT_OUTGOING_FAX:
    case History::EVENT_INCOMING_FAX:
        /* @var $task \app\models\obj\Fax */
        $fax = $model->objFax;
        echo $this->render('_item_common', [
            'history' => $model,
            'afterBody' => ' - ' .
                (isset($fax->document) ? Html::a(
                    Yii::t('app', 'view document'),
                    $fax->document->getViewUrl(),
                    [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]
                ) : ''),
            'footer' => Yii::t('app', '{type} was sent to {group}', [
                'type' => $fax ? $fax->getTypeText() : 'Fax',
                // Свойство $fax->creditorGroup не существует, поэтому пока что смело
                // выбрасываем код, либо тогда уже дописываем извлечение creditorGroup
                'group' => ''
            ]),
            'iconClass' => 'fa-fax bg-green'
        ]);
        break;

    case History::EVENT_CUSTOMER_CHANGE_TYPE:
        echo $this->render('_item_statuses_change', [
            'model' => $model,
            'oldValue' => Customer::getTypeTextByType($model->getDetailOldValue('type')),
            'newValue' => Customer::getTypeTextByType($model->getDetailNewValue('type'))
        ]);
        break;

    case History::EVENT_CUSTOMER_CHANGE_QUALITY:
        echo $this->render('_item_statuses_change', [
            'model' => $model,
            'oldValue' => Customer::getQualityTextByQuality($model->getDetailOldValue('quality')),
            'newValue' => Customer::getQualityTextByQuality($model->getDetailNewValue('quality')),
        ]);
        break;

    case History::EVENT_INCOMING_CALL:
    case History::EVENT_OUTGOING_CALL:
        /** @var Call $call */
        $call = $model->objCall;
        $answered = $call && $call->status == Call::STATUS_ANSWERED;

        echo $this->render('_item_common', [
            'history' => $model,
            'content' => $call->comment ?? '',
            // $call->applicant не существует поэтому удаляем footer
            'iconClass' => $answered ? 'md-phone bg-green' : 'md-phone-missed bg-red',
            // 'iconIncome' - не используется, удаляем
        ]);
        break;

    default:
        echo $this->render('_item_common', [
            'history' => $model,
            'iconClass' => 'fa-gear bg-purple-light'
        ]);
        break;
}
