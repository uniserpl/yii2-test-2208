<?php

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $task \app\models\obj\Task */

$task = $history->objModel;
echo $history->eventText . ': ' . ($task ? $task->title : '');
