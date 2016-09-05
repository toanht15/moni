<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_kpi extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_kpi';

    public $NeedAdminLogin = true;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);

    }

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var ManagerKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerKpiService');
        /** @var ManagerBrandKpiService $manager_brand_kpi_service */
        $manager_brand_kpi_service = $this->createService('ManagerBrandKpiService');

        $columns = $manager_kpi_service->getColumns();

        foreach ($columns as $column) {
            $data_csv_column[] = $column->name;
        }
        $date = array('Date');
        array_splice($data_csv_column, 0, 0, $date);

        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => $data_csv_column), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        if ($this->csv == ManagerKpiService::ALL_CSV_DOWNLOAD) {
            $get_dates = $manager_brand_kpi_service->getAscendingSummedDates();
            foreach ($get_dates as $date) {
                $this->Data['brand_kpi_values'][] = $manager_kpi_service->getManagerKpiByDate($date);;
            }
        }
        if ($this->csv == ManagerKpiService::GET_CSV_BY_DATE) {
            $get_dates = $manager_kpi_service->getManagerKpiDatesByFromDateAndToDate($this->from_date, $this->to_date);
            foreach ($get_dates as $date) {
                $this->Data['brand_kpi_values'][] = $manager_kpi_service->getManagerKpiByDate($date);;
            }
        }

        foreach ($this->Data['brand_kpi_values'] as $data_csv) {
            $array_data = $csv->out(array('data' => $data_csv), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        }
        exit();
    }
}