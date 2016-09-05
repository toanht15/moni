<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_action_open_email_rate extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_action_open_email_rate';

    public $NeedOption = array();

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
    }

    public function validate () {

    return true;
    }

    function doAction() {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');

        $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($this->Data['cp_id']);

        $cp = $cp_flow_service->getCpById($this->Data['cp_id']);

        if (!$cp) {
            return;
        }

        $brand = $brand_service->getBrandById($cp->brand_id);

        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => OpenEmailTrackingLogs::$csv_header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

        OpenEmailTrackingLogs::writeCSVFile($cp_action_groups, $cp_flow_service, $brand, $cp, $csv);

        exit();
    }
}
