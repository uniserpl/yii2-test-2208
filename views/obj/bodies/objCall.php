<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $call \app\models\obj\Call */

// Улучшаем читабельность длинной строки
$call = $history->objModel;
if (empty($call)) {
    echo '<i>Deleted</i> ';
    return;
}
echo $call->totalStatusText . (
    $call->getTotalDisposition(false)
    ? " <span class='text-grey'>"
        . $call->getTotalDisposition(false)
        . "</span>"
    : ""
);
