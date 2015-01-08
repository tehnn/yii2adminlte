<?php

namespace arturoliveira;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;

class ExcelView extends \kartik\grid\GridView {

    /**
     * Grid Export Formats
     */
    const FULL_HTML = 'html';
    const FULL_CSV = 'csv';
    const FULL_PDF = 'pdf';
    const FULL_TEXT = 'txt';
    const FULL_EXCEL = 'xls';
    const FULL_EXCELX = 'xlsx';

    /**
     * @array|boolean the grid export menu settings. Displays a Bootstrap dropdown menu that allows you to export the grid as
     * either html, csv, or excel. If set to false, will not be displayed. The following options can be set:
     * - label: string,the export menu label (this is not HTML encoded). Defaults to 'Export'.
     * - icon: string,the glyphicon suffix to be displayed before the export menu label. If set to an empty string, this
     *   will not be displayed. Defaults to 'export'.
     * - browserPopupsMsg: string, the message to be shown to disable browser popups for download
     * - options: array, HTML attributes for the export menu. Defaults to ['class' => 'btn btn-danger']
     */
    public $fullExport = [];

    /**
     * @var array the configuration for each export format. The array keys must be the one of the `format` constants
     * (CSV, HTML, TEXT, or EXCEL) and the array value is a configuration array consisiting of these settings:
     * - label: string,the label for the export format menu item displayed
     * - icon: string,the glyphicon suffix to be displayed before the export menu item label. If set to an empty string, this
     *   will not be displayed. Defaults to the 'floppy-' glyphicons present in bootstrap.
     * - showHeader: boolean, whether to show table header row in the output. Defaults to `true`.
     * - showPageSummary: boolean, whether to show table page summary row in the output. Defaults to `true`.
     * - showFooter: boolean, whether to show table footer row in the output. Defaults to `true`.
     * - showCaption: boolean, whether to show table caption in the output (only for HTML). Defaults to `true`.
     * - worksheet: string, the name of the worksheet, when saved as excel file.
     * - colDelimiter: string, the column delimiter string for TEXT and CSV downloads.
     * - rowDelimiter: string, the row delimiter string for TEXT and CSV downloads.
     * - filename: the base file name for the generated file. Defaults to 'grid-export'. This will be used to generate a default
     *   file name for downloading (extension will be one of csv, html, or xls - based on the format setting).
     * - alertMsg: string, the message prompt to show before saving. If this is empty or not set it will not be displayed.
     * - cssFile: string, the css file that will be used in the exported HTML file. Defaults to:
     *   `http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css`.
     * - options: array, HTML attributes for the export format menu item.
     */
    public $fullExportConfig = [];
    //Document properties
    public $creator = '';
    public $title = null;
    public $subject = 'Subject';
    public $description = '';
    public $category = '';
    //the PHPExcel object
    public $objPHPExcel = null;
    //config
    public $autoWidth = true;
    public $fullExportType = 'xls';
    public $filename = null; //export FileName
    public $stream = true; //stream to browser
    public $grid_mode = 'grid'; //Whether to display grid ot export it to selected format. Possible values(grid, export)
    public $grid_mode_var = 'grid_mode'; //GET var for the grid mode
    //buttons config
    public $exportButtonsCSS = 'summary';
#    public $exportButtons = array('Excel2007');
#    public $exportText = 'Export to: ';
    //callbacks
    public $onRenderHeaderCell = null;
    public $onRenderDataCell = null;
    public $onRenderFooterCell = null;
    public $headerStartRow = 1;
    public $tableStartRow = 1;
    public $tableEndColumn = 1;
    public $tableEndRow = 1;

    public function init() {
        Yii::setAlias('@excelview', dirname(__FILE__));
        Yii::$app->i18n->translations['excelview'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@excelview/messages',
            'forceTranslation' => true
        ];

        if ($this->fullExport !== false) {
            $this->fullExport = ArrayHelper::merge([
                        'label' => Yii::t('excelview', 'Full Export'),
                        'icon' => 'export',
                        'browserPopupsMsg' => Yii::t('excelview', 'Disable any popup blockers in your browser to ensure proper download.'),
                        'options' => ['class' => 'btn btn-danger']
                            ], $this->export);
            $defaultExportConfig = [
                self::FULL_HTML => [
                    'label' => Yii::t('excelview', 'HTML'),
                    'icon' => 'floppy-saved',
                    'filename' => Yii::t('excelview', 'grid-export'),
                    'options' => ['title' => Yii::t('excelview', 'Save as HTML')],
                    'Content-type' => 'text/html',
                    'extension' => 'html',
                    'PHPExcel_Writer' => 'HTML',
                    'header' => true,
                ],
                self::FULL_CSV => [
                    'label' => Yii::t('excelview', 'CSV'),
                    'icon' => 'floppy-open',
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                    'filename' => Yii::t('excelview', 'grid-export'),
                    'options' => ['title' => Yii::t('excelview', 'Save as CSV')],
                    'Content-type' => 'application/csv',
                    'extension' => 'csv',
                    'PHPExcel_Writer' => 'CSV',
                    'header' => false,
                ],
                self::FULL_PDF => [
                    'label' => Yii::t('excelview', 'PDF'),
                    'icon' => 'floppy-save',
                    'filename' => Yii::t('excelview', 'grid-export'),
                    'options' => ['title' => Yii::t('excelview', 'Save as PDF')],
                    'Content-type' => 'application/pdf',
                    'extension' => 'pdf',
                    'PHPExcel_Writer' => 'PDF',
                    'header' => true,
                ],
                self::FULL_EXCEL => [
                    'label' => Yii::t('excelview', 'Excel 95 and above'),
                    'icon' => 'floppy-remove',
                    'worksheet' => Yii::t('excelview', 'ExportWorksheet'),
                    'filename' => Yii::t('excelview', 'grid-export'),
                    'cssFile' => '',
                    'options' => ['title' => Yii::t('excelview', 'Save as Excel (xls)')],
                    'Content-type' => 'application/vnd.ms-excel',
                    'extension' => 'xls',
                    'PHPExcel_Writer' => 'Excel5',
                    'header' => true,
                ],
                self::FULL_EXCELX => [
                    'label' => Yii::t('excelview', 'Excel 2007 and above'),
                    'icon' => 'floppy-remove',
                    'worksheet' => Yii::t('excelview', 'ExportWorksheet'),
                    'filename' => Yii::t('excelview', 'grid-export'),
                    'cssFile' => '',
                    'options' => ['title' => Yii::t('excelview', 'Save as Excel (xlsx)')],
                    'Content-type' => 'application/vnd.ms-excel',
                    'extension' => 'xlsx',
                    'PHPExcel_Writer' => 'Excel2007',
                    'header' => true,
                ],
            ];
            if (is_array($this->fullExportConfig) && !empty($this->fullExportConfig)) {
                foreach ($this->fullExportConfig as $format => $setting) {
                    $setup = is_array($this->fullExportConfig[$format]) ? $this->fullExportConfig[$format] : [];
                    $exportConfig[$format] = empty($setup) ? $defaultExportConfig[$format] :
                            ArrayHelper::merge($defaultExportConfig[$format], $setup);
                }
                $this->fullExportConfig = $exportConfig;
            } else {
                $this->fullExportConfig = $defaultExportConfig;
            }
            foreach ($this->fullExportConfig as $format => $setting) {
                $this->fullExportConfig[$format]['options']['data-pjax'] = false;
            }
        }
        $params = \Yii::$app->request->queryParams;
        if (isset($params[$this->grid_mode_var]))
            $this->grid_mode = $params[$this->grid_mode_var];
        if (isset($params['exportType']))
            $this->fullExportType = $params['exportType'];
        if ($this->grid_mode == 'export') {
            #$this->title = $this->title ? $this->title : \yii\web\View::$title;
            $module = Yii::$app->getModule('gridview');
            $this->initColumns();
            //parent::init();
            //Autoload fix
#            spl_autoload_unregister(array('YiiBase', 'autoload'));
#            Yii::import($this->libPath, true);
            $this->objPHPExcel = new \PHPExcel();
#            spl_autoload_register(array('YiiBase', 'autoload'));
            // Creating a workbook
            $this->objPHPExcel->getProperties()->setCreator($this->creator);
            $this->objPHPExcel->getProperties()->setTitle($this->title);
            $this->objPHPExcel->getProperties()->setSubject($this->subject);
            $this->objPHPExcel->getProperties()->setDescription($this->description);
            $this->objPHPExcel->getProperties()->setCategory($this->category);
        } else
            parent::init();
    }

    /**
     * Renders the file header.
     * @return nothing
     */
    public function renderHeader() {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition == self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition == self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }
        $styleExcel95 = [
            'font' => [
                'bold' => TRUE,
                'color' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => [
                    'argb' => '00000000',
                ],
            ],
        ];
        $styleExcel2007 = [
            'font' => [
                'bold' => TRUE,
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'startcolor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endcolor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        if ($this->fullExportConfig[$this->fullExportType]['header']) {
            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName(1) . $this->tableStartRow, Yii::$app->name, true);
            $this->tableStartRow++;
            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName(1) . $this->tableStartRow, Yii::t('excelview', 'Data exported on: {timestamp}', ['timestamp' => Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd HH:mm:ss')]), true);
            $this->tableStartRow++;
            $this->tableStartRow++;
        }
        $this->tableEndColumn = 0;
        foreach ($this->columns as $column) {
            $this->tableEndColumn = $this->tableEndColumn + 1;
            $provider = $column->grid->dataProvider;

            if ($column->header === null) {
                if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                    /* @var $model Model */
                    $model = new $provider->query->modelClass;
                    $head = $model->getAttributeLabel($column->attribute);
                } else {
                    $models = $provider->getModels();
                    if (($model = reset($models)) instanceof Model) {
                        /* @var $model Model */
                        $head = $model->getAttributeLabel($column->attribute);
                    } else {
                        $head = Inflector::camel2words($column->attribute);
                    }
                }
            } else {
                $head = $column->header;
            }

            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($this->tableEndColumn) . $this->tableStartRow, $head, true);
            // Apply formatting to header cell
            if ($this->fullExportType == self::FULL_EXCELX)
                $cell = $this->objPHPExcel->getActiveSheet()->getStyle($this->columnName($this->tableEndColumn) . $this->tableStartRow)->applyFromArray($styleExcel2007);
            else
                $cell = $this->objPHPExcel->getActiveSheet()->getStyle($this->columnName($this->tableEndColumn) . $this->tableStartRow)->applyFromArray($styleExcel95);
        }
        for ($i = $this->headerStartRow; $i < ($this->tableStartRow-1); $i++) {
            $this->objPHPExcel->getActiveSheet()->mergeCells($this->columnName(1) . $i . ":" . $this->columnName($this->tableEndColumn) . $i);
            if ($this->fullExportType == self::FULL_EXCELX)
                $cell = $this->objPHPExcel->getActiveSheet()->getStyle($this->columnName(1) . $i)->applyFromArray($styleExcel2007);
            else
                $cell = $this->objPHPExcel->getActiveSheet()->getStyle($this->columnName(1) . $i)->applyFromArray($styleExcel95);
        }
        // Freeze the top row
        $this->objPHPExcel->getActiveSheet()->freezePane($this->columnName(1) . ($this->tableStartRow+1));
    }

    /**
     * Renders the file body.
     * @return the number of file rows.
     */
    public function renderBody() {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];

            $rows[] = $this->renderRow($model, $key, $index);
        }
        $this->tableEndRow = count($rows);
        // Set autofilter on
        $this->objPHPExcel->getActiveSheet()->setAutoFilter(
                $this->columnName(1).
                ($this->tableStartRow).
                ":".
                $this->columnName($this->tableEndColumn).
                $this->tableEndRow
        );
        if (!empty($rows)) {
            return count($models);
        }
    }

    /**
     * Renders a file row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return nothing
     */
    public function renderRow($model, $key, $index) {
        $cells = [];
        /* @var $column Column */
        $this->tableEndColumn = 0;
        foreach ($this->columns as $column) {
            if($column instanceof \yii\grid\SerialColumn || $column instanceof \kartik\grid\SerialColumn)
                $value = $column->renderDataCell($model, $key, $index);
            elseif ($column->filterType === \kartik\grid\GridView::FILTER_COLOR) {
                $value = $model[$column->attribute];
                $value = $value === null ? "" : $value; #$column->grid->getFormatter()->format($value, 'raw');
            } elseif ($column->attribute !== null) {
                $value = $model[$column->attribute];
                $value = $value === null ? "" : $value; #$column->grid->getFormatter()->format($value, 'raw');
            } else
                $value = $column->renderDataCellContent();
            $this->tableEndColumn++;
            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($this->tableEndColumn) . ($index + $this->tableStartRow + 1), strip_tags($value), true);
        }
        return;
    }

    public function renderFooter($row) {
        $this->tableEndColumn = 0;
        foreach ($this->columns as $n => $column) {
            $this->tableEndColumn = $this->tableEndColumn + 1;
            if ($column->footer) {
                $footer = trim($column->footer) !== '' ? $column->footer : $column->grid->blankDisplay;

                $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($this->tableEndColumn) . ($row + 2), $footer, true);
                if (is_callable($this->onRenderFooterCell))
                    call_user_func_array($this->onRenderFooterCell, array($cell, $footer));
            }
        }
    }

    public function run() {
        if ($this->grid_mode == 'export') {
            $this->dataProvider->pagination = FALSE;
            $this->renderHeader();
            $row = $this->renderBody();
            $this->renderFooter($row);
            //set auto width
            if ($this->autoWidth)
                foreach ($this->columns as $n => $column)
                    $this->objPHPExcel->getActiveSheet()->getColumnDimension($this->columnName($n + 1))->setAutoSize(true);
            // Set the pdf renderer
            $rendererName = \PHPExcel_Settings::PDF_RENDERER_DOMPDF;
            //create writer for saving
            $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->fullExportConfig[$this->fullExportType]['PHPExcel_Writer']);
            if (!$this->stream)
                $objWriter->save($this->filename);
            else { //output to browser
                if (!$this->filename)
                    $this->filename = $this->title;
                ob_end_clean();
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-type: ' . $this->fullExportConfig[$this->fullExportType]['Content-type']);
                header('Content-Disposition: attachment; filename="' . $this->fullExportConfig[$this->fullExportType]['filename'] . '.' . $this->fullExportConfig[$this->fullExportType]['extension'] . '"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                Yii::$app->end();
            }
        } else {
            if (strpos($this->layout, '{fullexport}') > 0) {
                $this->layout = strtr($this->layout, [
                    '{fullexport}' => $this->renderFullExport(),
                    '{toolbar}' => $this->toolbar
                ]);
            } else {
                $this->layout = strtr($this->layout, ['{toolbar}' => $this->toolbar]);
            }
            parent::run();
        }
    }

    /**
     * Returns the corresponding excel column.(Abdul Rehman from yii forum)
     * 
     * @param int $index
     * @return string
     */
    public function columnName($index) {
        --$index;
        if ($index >= 0 && $index < 26)
            return chr(ord('A') + $index);
        else if ($index > 25)
            return ($this->columnName($index / 26)) . ($this->columnName($index % 26 + 1));
        else
            throw new Exception("Invalid Column # " . ($index + 1));
    }

    /**
     * Renders the server side export menu
     *
     * @return string
     */
    public function renderFullExport() {
        if ($this->fullExport === false || !is_array($this->fullExport)) {
            return '';
        }
        $formats = $this->fullExportConfig;
        if (empty($formats) || !is_array($formats)) {
            return '';
        }
        $title = $this->fullExport['label'];
        $icon = $this->fullExport['icon'];
        $options = $this->fullExport['options'];
        $items = [];
        $queryParams = Yii::$app->request->queryParams;
        if (array_key_exists('exportType', $queryParams))
            unset($queryParams['exportType']);
        if (array_key_exists($this->grid_mode_var, $queryParams))
            unset($queryParams[$this->grid_mode_var]);
        foreach ($formats as $format => $setting) {
            if (array_key_exists('query', $queryParams))
                $url = Url::to(ArrayHelper::merge([Yii::$app->request->pathInfo, 'query' => $queryParams['query'], 'exportType' => $format, $this->grid_mode_var => 'export'], $queryParams));
            else
                $url = Url::to(ArrayHelper::merge([Yii::$app->request->pathInfo, 'exportType' => $format, $this->grid_mode_var => 'export'], $queryParams));
            $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] : '<i class="glyphicon glyphicon-' . $setting['icon'] . '"></i> ' . $setting['label'];
            $items[] = ['label' => $label, 'url' => $url, 'linkOptions' => ['data-pjax' => 0]];
        }
        $title = ($icon == '') ? $title : "<i class='glyphicon glyphicon-{$icon}'></i> {$title}";
        return ButtonDropdown::widget([
                    'label' => $title,
                    'dropdown' => ['items' => $items, 'encodeLabels' => false],
                    'options' => $options,
                    'encodeLabel' => false
        ]);
    }

}
