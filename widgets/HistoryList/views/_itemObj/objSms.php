<?php

use app\models\obj\Sms;

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $sms Sms */

$sms = $history->objModel;
echo $this->render('../_item_common', [
    'history' => $history,
    'footer' => empty($sms) ? '' : ($sms->direction == Sms::DIRECTION_INCOMING
        ? Yii::t('app', 'Incoming message from {number}', ['number' => $sms->phone_from ?? ''])
        : Yii::t('app', 'Sent message to {number}', ['number' => $sms->phone_to ?? ''])),
    'iconClass' => 'icon-sms bg-dark-blue'
]);
