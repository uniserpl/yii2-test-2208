<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $task \app\models\obj\Task */

$task = $history->objModel;

echo $this->render('../_item_common', [
    'history' => $history,
    'iconClass' => 'fa-check-square bg-yellow',
    'footer' => isset($task->customerCreditor->name) ? "Creditor: " . $task->customerCreditor->name : ''
]);
