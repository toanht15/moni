<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.services.SegmentService');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');

class api_show_user_list_count extends BrandcoGETActionBase {
    protected $ContainerName = 'api_show_user_list_pager';

    public $NeedOption = array();
    protected $AllowContent = array('JSON');
    protected $brand;

    public function doThisFirst() {
        $this->brand = $this->getBrand();
    }

    public function validate() {
        if(!$this->action_id && !$this->cp_id) {
            return true;
        }
        $validatorService = new CpValidator($this->brand->id);
        if (!$validatorService->isOwner($this->cp_id)) {
            return false;
        }
        if (!$validatorService->isOwnerOfAction($this->action_id)) {
            return false;
        }
        return true;
    }

    function doAction() {
        if($this->cp_id) {
            $search_condition = $this->getSearchConditionSession($this->cp_id);
        } else {
            $search_condition = $this->getBrandSession('searchBrandCondition');
        }

        if($this->cp_id) {
            /** @var CpMessageDeliveryService $message_delivery_service */
            $message_delivery_service = $this->createService('CpMessageDeliveryService');

            // 現在の予約情報を取得
            $current_reservation = $message_delivery_service->getOrCreateCurrentReservation($this->brand->id, $this->action_id);
        }

        $page_info = array(
            'cp_id'             => $this->cp_id,
            'action_id'         => $this->action_id,
            'page_no'           => $this->page_no ? $this->page_no : '1',
            'brand_id'          => $this->brand->id,
            'tab_no'            => $this->tab_no,
            'reservation_id'    => $current_reservation->id,
        );

        $show_segment_tooltip = false;
        //Segment Message Action
        if($this->cp_id) {

            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->createService('CpFlowService');
            $cp = $cp_flow_service->getCpById($this->cp_id);

            if($cp->isCpTypeMessage()) {

                /** @var SegmentService $segment_service */
                $segment_service = $this->createService('SegmentService');

                $segment_condition_session = $this->getBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY);
                $provision_ids = $segment_service->getProvisionIdsFromSession($segment_condition_session);

                if($provision_ids) {
                    $previous_date = strtotime('yesterday');
                    $cur_date = strtotime('today');

                    $create_date_array = array($previous_date, $cur_date);

                    $search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION] = array(
                        'create_dates' => $create_date_array,
                        'provision_ids' => $provision_ids,
                    );

                    $show_segment_tooltip = true;
                }
            }
        }

        /** @var CpCreateSqlService $create_sql_service */
        $create_sql_service = $this->createService("CpCreateSqlService");
        $count_sql = $create_sql_service->getUserSql($page_info, $search_condition, '', true, null);

        $db = new aafwDataBuilder();
        $user_count = $db->getBySQL($count_sql, array());

        if($this->cp_id) {
            //開いているページ内の未送信の人数
            $page_not_sent_user_count = $this->fan_count - $this->page_sent_user_count;
            //全体の未送信人数
            $all_not_sent_user_count = $user_count[0]['total_count'] - $user_count[0]['sent_count'];
        }

        $html[0] = aafwWidgets::getInstance()->loadWidget('BrandcoUserListPager')->render(array(
            'TotalCount'       => $user_count[0]['total_count'],
            'CurrentPage'      => $this->page_no,
            'Count'            => $this->limit ? $this->limit : CpCreateSqlService::DISPLAY_50_ITEMS,
            'search_condition' => $this->getSearchConditionSession($this->cp_id),
            'reservation_id'   => $current_reservation->id,
            'action_id'        => $this->action_id,
            'show_segment_tooltip' => $show_segment_tooltip
        ));

        $parser = new PHPParser();
        $html[1] = $parser->parseTemplate('NotUserListCount.php', array(
            'total_count'              => $user_count[0]['total_count'],
            'all_not_sent_user_count'  => $all_not_sent_user_count,
            'page_not_sent_user_count' => $page_not_sent_user_count,
        ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
