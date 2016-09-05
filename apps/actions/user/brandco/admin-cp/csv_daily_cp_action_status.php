<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_daily_cp_action_status extends BrandcoGETActionBase {
    protected $ContainerName = 'csv_daily_action_status';

    public $NeedOption = array();

    public $NeedAdminLogin = true;

    public static $DownloadType = array('read', 'finish');

    public function doThisFirst() {
        $this->cp_id = $this->GET['exts'][0];
    }

    public function validate() {

        $cp_validator = new CpValidator($this->brand->id);
        if (!$cp_validator->isOwner($this->cp_id)) {
            return false;
        }

        if (!in_array($this->GET['type'], self::$DownloadType)) {
            return false;
        }

        return true;
    }

    function doAction() {

        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_actions = $cp_flow_service->getCpActionsByCpId($this->cp_id);

        $cp = $cp_flow_service->getCpById($this->cp_id);
        $today = date('Y/m/d');
        $start_date = date('Y/m/d', strtotime($cp->start_date));
        if ($cp->isPermanent()) {
            $limit_date = $today;
        } else {
            $end_date;
            if ($cp->announce_date == '0000-00-00 00:00:00') {
                // admin-cp/edit_setting_basicと同様の算出方法にした
                $end_date = date('Y/m/d H:i:s', time() + 87000);
            } else {
                $end_date = date('Y/m/d', strtotime($cp->announce_date));
            }
            $limit_date = $today < $end_date ? $today : $end_date;
        }

        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_label = array('日付');
        $item_no = 1;
        foreach ($cp_actions as $cp_action) {
            $action_type_detail = $cp_action->getCpActionDetail();
            $array_label[] = $item_no++ . '.' . $action_type_detail['title'];
        }

        $csv_date = $start_date;
        $db = new aafwDataBuilder();
        $array_result = array();
        while ($csv_date <= $limit_date) {
            $array_value = array();
            $array_value[] = $csv_date;
            foreach ($cp_actions as $cp_action) {
                if ($this->GET['type'] == 'read') {
                    $sql = $this->getReadCountSQL($cp_action->id, $csv_date);
                } else if ($this->GET['type'] == 'finish') {
                    $sql = $this->getFinishCountSQL($cp_action->id, $csv_date);
                }
                $result = $db->getBySQL($sql, null);
                $array_value[] = intval($result[0]['cnt']);
            }
            $array_result[] = $array_value;
            $csv_date = date('Y/m/d', strtotime("{$csv_date} +1 day"));
        }

        print $csv->out(array('list' => $array_result, 'header' => $array_label, '__ENCODING__' => 'Shift_JIS'), 1);
        exit;
    }

    private function getReadCountSQL($cp_action_id, $csv_date) {
        return "SELECT count(id) AS 'cnt'
                FROM cp_user_action_messages
                WHERE updated_at BETWEEN '{$csv_date} 00:00:00' AND '{$csv_date} 23:59:59'
                AND read_flg = 1
                AND del_flg = 0
                AND cp_action_id = {$cp_action_id}";
    }

    private function getFinishCountSQL($cp_action_id, $csv_date) {
        return "SELECT count(id) AS 'cnt'
                FROM cp_user_action_statuses
                WHERE updated_at BETWEEN '{$csv_date} 00:00:00' AND '{$csv_date} 23:59:59'
                AND status = 1
                AND del_flg = 0
                AND cp_action_id = {$cp_action_id}";
    }
}
