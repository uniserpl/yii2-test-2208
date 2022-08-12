<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\History */

echo $this->render('_itemObj/' . $model->getObjName('unknown'), ['history' => $model]);
