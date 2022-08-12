<?php

use app\models\obj\Call;

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $call Call */

$call = $history->objModel;
$answered = $call && $call->status == Call::STATUS_ANSWERED;

echo $this->render('../_item_common', [
    'history' => $history,
    'content' => $call->comment ?? '',
    'iconClass' => $answered ? 'md-phone bg-green' : 'md-phone-missed bg-red',
]);
