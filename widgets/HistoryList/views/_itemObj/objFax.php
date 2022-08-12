<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $fax \app\models\obj\Fax */

$fax = $history->objModel;
echo $this->render('../_item_common', [
    'history' => $history,
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
        'group' => ''
    ]),
    'iconClass' => 'fa-fax bg-green'
]);
