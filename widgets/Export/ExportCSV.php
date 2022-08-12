<?php

namespace app\widgets\Export;

use yii\grid\GridView;

/**
 * Экспорт гигантского CSV
 *
 * @property \yii\data\BaseDataProvider $dataProvider
 */
class ExportCSV extends GridView
{
    public $filename;
    public $timeout = -1;
    public $batchSize = -1;

    public $csvDelimiter = ",";
    public $csvEnclosure = '"';
    public $csvEscapeChar = '\\';

    private $currentPage = 0;

    /**
     * Генерация строки в csv-формате
     * 
     * @param string[] $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escapeChar
     * @return string
     */
    public static function csv2str($fields, $delimiter = ";", $enclosure = '"', $escapeChar = '\\')
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $fields, $delimiter, $enclosure, $escapeChar);
        rewind($buffer);
        $csv = fgets($buffer);
        fclose($buffer);
        return $csv;
    }

    public function run()
    {
        $this->initPage();

        $filename = preg_replace(
            '/(.csv)?$/i',
            '.csv',
            $this->filename ?: 'export-' . time()
        );

        // Start sending CSV-file
        header("Content-Disposition: attachment; filename=$filename;");
        header("Content-Type: text/csv");

        if ($this->timeout >= 0) {
            set_time_limit($this->timeout);
        }

        echo $this->renderTableHeader();

        do {
            echo $this->renderTablePage();
        } while ($this->nextPage());

        // Break sending CSV-file and anything
        exit;
    }

    public function initPage()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination) {
            if ($this->batchSize > 0) {
                $pagination->setPageSize($this->batchSize);
            }
            $pagination->setPage($this->currentPage);
        }
    }

    public function nextPage()
    {
        if (0 === $this->dataProvider->getCount()) {
            return false;
        }
        $pagination = $this->dataProvider->getPagination();
        if (!$pagination) {
            return false;
        }
        $this->currentPage = $this->currentPage + 1;
        $pagination->setPage($this->currentPage);

        /* BaseDataProvider */
        $this->dataProvider->setModels(null);
        $this->dataProvider->setKeys(null);
        return true;
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column \yii\grid\DataColumn */
            $cells[] = trim(strip_tags($column->renderHeaderCell()));
        }

        return self::csv2str(
            $cells,
            $this->csvDelimiter,
            $this->csvEnclosure,
            $this->csvEscapeChar
        );
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTablePage()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();

        $rows = [];
        foreach ($models as $index => $model) {
            $rows[] = $this->renderTableRow($model, $keys[$index], $index);
        }
        return implode('', $rows);
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];

        /* @var $column \yii\grid\DataColumn */
        foreach ($this->columns as $column) {
            if ($column->content === null) {
                $cells[] = $this->formatter->format(
                    $column->getDataCellValue($model, $key, $index),
                    $column->format
                );
            } else {
                $cells[] = call_user_func($this->content, $model, $key, $index, $this);
            }
        }

        return self::csv2str(
            $cells,
            $this->csvDelimiter,
            $this->csvEnclosure,
            $this->csvEscapeChar
        );
    }
}
