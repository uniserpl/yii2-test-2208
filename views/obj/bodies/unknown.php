<?php

use app\models\History;
use app\models\Customer;

/* @var $this \yii\web\View */
/* @var $history History */
/* @var $obj \app\models\ObjBasic */

// $obj = $history->objModel;
switch ($history->event) {
    case History::EVENT_CUSTOMER_CHANGE_TYPE:
        echo $history->eventText . ' ' .
            (Customer::getTypeTextByType($history->getDetailOldValue('type')) ?? 'not set') . ' to ' .
            (Customer::getTypeTextByType($history->getDetailNewValue('type')) ?? 'not set');
        break;

    case History::EVENT_CUSTOMER_CHANGE_QUALITY:
        echo $history->eventText . ' ' .
            (Customer::getQualityTextByQuality($history->getDetailOldValue('quality')) ?? 'not set') . ' to ' .
            (Customer::getQualityTextByQuality($history->getDetailNewValue('quality')) ?? 'not set');
        break;

    default:
        echo $history->eventText;
        break;
}
