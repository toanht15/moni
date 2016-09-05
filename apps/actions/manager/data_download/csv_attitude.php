<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.data_operation.extractor.AttitudeChangingExtractor');
AAFW::import('jp.aainc.classes.data_operation.writer.CsvFileWriter');

class csv_attitude extends BrandcoManagerPOSTActionBase {

    public $NeedOption = [];
    protected $ContainerName = 'brand_csv';
    protected $Form = [
        'package' => 'dashboard',
        'action' => 'brand_csv/{brandId}',
    ];

    protected $ValidatorDefinition = [
        'attitudeChangeFrom' => [
            'type'     => 'date',
        ],
        'attitudeChangeTo' => [
            'type'     => 'date',
        ]
    ];


    public function validate () {
        return true;
    }

    public function doAction() {

        $input = [
            'brand_id' => $this->brandId,
            'date_from' => $this->attitudeChangeFrom,
            'date_to' => $this->attitudeChangeTo
        ];

        $filename = isset($this->filename) ? $this->filename : date('YmdHis');

        // ストリームデータとして出力
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $writer = new CsvFileWriter();
        $extractor = new AttitudeChangingExtractor();

        // ブラウザに直接書き込み
        $writer->write('php://output', $extractor->getCsvData($input));

        // 一行ずつ吐く機能が無いからしょうがない
        exit();
    }
}
