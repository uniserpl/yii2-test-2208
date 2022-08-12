<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $call \app\models\obj\Call */

$call = $history->objModel;
if (empty($call)) {
    echo '<i>Deleted</i> ';
    return;
}

// Улучшаем читабельность длинной строки
// Вобще-то параметр false приводит к пустому результату, но пока оставим так.
$totalDisposition = $call->getTotalDisposition(false);

echo $call->totalStatusText . (
    $totalDisposition
    ? " <span class='text-grey'>$totalDisposition</span>"
    : ""
);
