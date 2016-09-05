<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_fid_daily_report extends BrandcoGETActionBase {
    protected $ContainerName = 'csv_fid_daily_report';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '256M');

        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['brand'] = $this->getBrand();
    }

    public function validate() {

        $cp_validator = new CpValidator($this->Data['brand']->id);
        if (!$cp_validator->isOwner($this->Data['cp_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        if(!$this->isLoginManager() && !$brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CAN_GET_FID_REPORT)) {
            return false;
        }

        $db = new aafwDataBuilder();
        $sql = "SELECT from_id FROM cp_users WHERE cp_id = ?cp_id? GROUP BY from_id";
        $from_ids = $db->getBySQL($sql, array(array('cp_id' => $this->Data['cp_id'])));

        $header = array('date');
        foreach($from_ids as $from_id) {
            if($from_id['from_id']) {
                $header[] = $from_id['from_id'];
            } else{
                $header[] = '計測なし';
            }
        }

        // Export csv
        $csv = new CSVParser();

        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => $header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");


        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp = $cp_flow_service->getCpById($this->Data['cp_id']);
        $start_date = $cp->public_date;
        if ($cp->isPermanent()) {
            $end_date = date('Y-m-d 23:59:59');
        } else {
            $end_date = $cp->announce_date;
        }
        $i = 0;
        while(strtotime($start_date) <= strtotime($end_date)) {

            $result = array();

            $date = date("Y-m-d", strtotime($start_date));
            $result[] = $date;
            foreach($from_ids as $from_id) {
                $sql = "SELECT COUNT(1) AS count ";
                $sql .= " FROM cp_users ";
                $sql .= " WHERE from_id = ?from_id? AND cp_id = ?cp_id?";
                $sql .= " AND created_at > ?start_at? AND created_at <= ?end_at?";
                $datas = $db->getBySQL($sql, array(array(
                    'start_at' => $date." 00:00:00",
                    'end_at' => $date." 23:59:59",
                    'from_id' => $from_id['from_id'],
                    'cp_id' => $this->Data['cp_id'],
                )));
                $result[] = intval($datas[0]['count']);
            }

            $array_data = $csv->out(array('data' => $result), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

            $start_date = $cp->public_date.' +'.++$i.' day';
        }

        exit();
    }
}
