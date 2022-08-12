<?php

use app\models\History;
use app\models\Customer;

/* @var $this \yii\web\View */
/* @var $history History */
/* @var $obj \app\models\ObjBasic */

// $obj = $history->objModel;
switch ($history->event) {
    case History::EVENT_CUSTOMER_CHANGE_TYPE:
        echo $this->render('../_item_statuses_change', [
            'history' => $history,
            'oldValue' => Customer::getTypeTextByType($history->getDetailOldValue('type')),
            'newValue' => Customer::getTypeTextByType($history->getDetailNewValue('type'))
        ]);
        break;

    case History::EVENT_CUSTOMER_CHANGE_QUALITY:
        echo $this->render('../_item_statuses_change', [
            'history' => $history,
            'oldValue' => Customer::getQualityTextByQuality($history->getDetailOldValue('quality')),
            'newValue' => Customer::getQualityTextByQuality($history->getDetailNewValue('quality')),
        ]);
        break;

    default:
        echo $this->render('../_item_common', [
            'history' => $history,
            'iconClass' => 'fa-gear bg-purple-light'
        ]);
        break;
}
