<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_brand_kpi extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_campaign_list';

    public $NeedAdminLogin = true;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);

    }

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var ManagerBrandKpiService $manager_kpi_service */
        $manager_brand_kpi_service = $this->createService('ManagerBrandKpiService');
        $brand_service = $this->createService('BrandService');
        $columns = $manager_brand_kpi_service->getColumns();
        $brand_id = $this->GET['exts'][0];
        foreach($columns as $column){
            $data_csv_column[] = $column->name;
        }
        $date = array('Date');
        array_splice($data_csv_column,0,0,$date);

        // Export csv
        $csv = new CSVParser();
        $brand_name = $brand_service->getBrandById($brand_id)->name;
        $data_brand_info['brand_name'] = "Brand Name: $brand_name";
        $array_data = $csv->out(array('data' => $data_brand_info), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        header("Content-type:" . $csv->getContentType());
        $csv->setCSVFileName($brand_name.'-'.date( 'YmdHis' ));
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => $data_csv_column), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        $get_dates = $manager_brand_kpi_service->getAscendingSummedDates();
        foreach ($get_dates as $date) {
            $brand_kpi_values = array();
            $get_brand_kpi_values = $manager_brand_kpi_service->getAllBrandValuesByBrandIdAndSummeddate($brand_id, $date->summed_date);
            $brand_kpi_values[] = $date->summed_date;
            foreach ($get_brand_kpi_values as $value) {
                $brand_kpi_values[] = intval($value->value);
            }
            $this->Data['brand_kpi_values'][] = $brand_kpi_values;
        }
            foreach($this->Data['brand_kpi_values'] as $item){
                $data_csv = array();
                foreach($item as $val){
                    $data_csv[] = $val;
                }
                $array_data = $csv->out(array('data' => $data_csv), 1);
                print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
            }
        exit();
    }
}
