<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_brand_open_email_rate extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_brand_open_email_rate';

    public $NeedOption = array();

    public function doThisFirst() {
        $this->Data['brand_id'] = $this->GET['exts'][0];
    }

    public function validate () {

    return true;
    }

    function doAction() {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');

        $cps = $cp_flow_service->getCpsNotDraftByBrandId($this->Data['brand_id']);

        if (!$cps) {
            return;
        }

        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => OpenEmailTrackingLogs::$csv_header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

        foreach ($cps as $cp) {

            $brand = $brand_service->getBrandById($cp->brand_id);

            $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($cp->id);

            OpenEmailTrackingLogs::writeCSVFile($cp_action_groups, $cp_flow_service, $brand, $cp, $csv);
        }
        exit();
    }
}
