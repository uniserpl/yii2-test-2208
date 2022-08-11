<?php

namespace app\widgets\HistoryList;

use app\models\search\HistorySearch;
use app\widgets\Export\Export;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class HistoryList extends Widget
{
    /**
     * @return string
     */
    public function run()
    {
        $model = new HistorySearch();

        return $this->render('main', [
            'model' => $model,
            'linkExport' => $this->getLinkExport(),
            'dataProvider' => $model->search(Yii::$app->request->queryParams)
        ]);
    }

    /**
     * @return string
     */
    private function getLinkExport()
    {
        return Url::to(ArrayHelper::merge(
            [
                'site/export',
                'exportType' => Export::FORMAT_CSV
            ],
            Yii::$app->getRequest()->getQueryParams())
        );
    }
}
