<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class message_history extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_MESSAGE_HISTORY;
    private $limit = 20;
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'message_history',
    );

    public function validate() {
        return true;
    }

    function doAction() {
        $this->Data['message'] = $this->message_id;
        $this->Data['client_id'] = $this->client_id;
        $this->Data['delivered_date'] = $this->delivered_date;
        $this->Data['header'] = OpenEmailTrackingLogs::$csv_header;
        $this->Data['test_page'] = $this->test_page;

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

        if ($this->test_page == BrandService::COMPANY) {
            $condition['TEST_PAGE'] = '__ON__';
            $condition['test_page'] = $this->test_page;
        }

        if ($this->from_date) {
            $condition['FROM_DATE'] = '__ON__';
            $condition['delivered_date_from'] = date("Y-m-d 00:00:00", strtotime($this->from_date));
        }

        if ($this->to_date) {
            $condition['TO_DATE'] = '__ON__';
            $condition['delivered_date_to'] = date("Y-m-d 23:59:59", strtotime($this->to_date));
        }

        $condition['IS_LIMIT'] = '__ON__';
        $db = new aafwDataBuilder();
        $condition['__NOFETCH__'] = true;
        $rs = $db->getMessageHistory($condition);

        $count_limit = 0;
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
            $message_history = array();
            $message_history['message_id'] = $cp['id'];
            $message_history['message_type'] = $cp_title;
            $message_history['type'] = ($cp['type'] == Cp::TYPE_CAMPAIGN) ? 'キャンペーン' : 'メッセージ';
            $message_history['account_name'] = $cp['name'];
            $message_history['brand_id'] = $cp['brand_id'];
            $message_history['delivery_date'] = date('Y-m-d H:i', strtotime($cp['updated_at']));
            $message_history['delivery_sum'] = $target_count;
            $message_history['open_sum'] = $opened_email_count ? $opened_email_count : '-';
            $message_history['brandco_access_rate'] = $thread_view_from_email ? $thread_view_from_email : '-';
            $message_history['message_read_sum'] = $cp_member_count[CpUserService::CACHE_TYPE_READ_PAGE];
            $message_history['message_been_read_sum'] = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION];
            if (is_float($message_read_rate)) {
                $message_history['message_read_rate'] = number_format($message_read_rate, 2) . '%';
            } else {
                $message_history['message_read_rate'] = $message_read_rate != 0 ? $message_read_rate . '%' : '-';
            }
            if (is_float($message_delivery_rate)) {
                $message_history['message_delivery_rate'] = number_format($message_delivery_rate, 2) . '%';
            } else {
                $message_history['message_delivery_rate'] = $message_delivery_rate != 0 ? $message_delivery_rate . '%' : '-';
            }
            $this->Data['message_history'][] = $message_history;

            $count_limit++;
            if ($count_limit == $this->limit) {
                $this->Data['message_limit'] = "件数が20件を超えたので表示を停止しました。CSVをダウンロードして閲覧してください。";
                break;
            }
        }

        return 'manager/dashboard/message_history.php';
    }
}