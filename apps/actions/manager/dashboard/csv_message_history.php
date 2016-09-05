<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_message_history extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_read_message';

    public $NeedOption = array();

    public function validate() {

        return true;
    }

    function doAction() {
        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => OpenEmailTrackingLogs::$csv_header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        $condition = array();
        if ($this->message_id != null) {
            $condition['MESSAGE'] = '__ON__';
            $condition['message'] = $this->message_id;
        }

        if ($this->client_id != null) {
            $condition['CLIENT'] = '__ON__';
            $condition['client_id'] = $this->client_id;
        }

        if ($this->from_date) {
            $condition['FROM_DATE'] = '__ON__';
            $condition['delivered_date_from'] = date("Y-m-d 00:00:00", strtotime($this->from_date));
        }

        if ($this->to_date) {
            $condition['TO_DATE'] = '__ON__';
            $condition['delivered_date_to'] = date("Y-m-d 23:59:59", strtotime($this->to_date));
        }

        if ($this->test_page == null) {
            $condition['TEST_PAGE'] = '__ON__';
        }

        $db = new aafwDataBuilder();
        $condition['__NOFETCH__'] = true;
        $rs = $db->getMessageHistory($condition);

        while ($cp = $db->fetch($rs)) {
            $cp_first_action = $cp_flow_service->getFirstActionOfCp($cp['cp_id']);
            $cp_action = $cp_flow_service->getCpActionById($cp['id']);
            $cp_title = $cp_first_action->getCpActionData()->title;
            $opened_email_count = $cp['open_log_count'];
            $thread_view_from_email = $cp['click_log_count'];
            $cp_member_count = $cp_action->getMemberCount();
            $target_count = $cp['target_count'];
            $message_read_rate = $target_count ? ($opened_email_count * 100 / $target_count) : '-';
            $message_delivery_rate = $target_count ? ($cp_member_count[CpUserService::CACHE_TYPE_READ_PAGE] * 100 / $target_count) : '-';
            $data_csv = array();
            $data_csv[] = intval($cp['id']);
            $data_csv[] = $cp_title;
            $data_csv[] = ($cp['type'] == Cp::TYPE_CAMPAIGN) ? 'キャンペーン' : 'メッセージ';
            $data_csv[] = $cp['name'];
            $data_csv[] = $cp['brand_id'];
            $data_csv[] = date('Y-m-d H:i', strtotime($cp['updated_at']));
            $data_csv[] = intval($target_count);
            $data_csv[] = $opened_email_count ? intval($opened_email_count) : '-';
            $data_csv[] = $thread_view_from_email ? intval($thread_view_from_email) : '-';
            $data_csv[] = intval($cp_member_count[CpUserService::CACHE_TYPE_READ_PAGE]);
            $data_csv[] = intval($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]);
            if (is_float($message_read_rate)) {
                $data_csv[] = number_format($message_read_rate, 2) . '%';
            } else {
                $data_csv[] = $message_read_rate != 0 ? $message_read_rate . '%' : '-';
            }
            if (is_float($message_delivery_rate)) {
                $data_csv[] = number_format($message_delivery_rate, 2) . '%';
            } else {
                $data_csv[] = $message_delivery_rate != 0 ? $message_delivery_rate . '%' : '-';
            }
            $array_data = $csv->out(array('data' => $data_csv), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        }
        exit();
    }
}
