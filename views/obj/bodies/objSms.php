<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $sms \app\models\obj\Sms */

$sms = $history->objModel;
echo $sms ? $sms->message : '';
