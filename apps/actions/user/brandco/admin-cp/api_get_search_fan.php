<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpMessageDeliveryService');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.entities.BrandOption');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.services.SegmentService');

class api_get_search_fan extends BrandcoGETActionBase {
    protected $ContainerName = 'api_get_search_fan';

    public $NeedOption = array();
    protected $AllowContent = array('JSON');

    public function doThisFirst() {
        if(!$this->cp_id || !$this->action_id) return 404;

        $this->Data['list_page'] = array(
            'cp_id'             => $this->cp_id,
            'action_id'         => $this->action_id, // URLのGETパラメータのアクションID
            'page_no'           => $this->page_no ? $this->page_no : '1',
            'display_action_id' => $this->display_action_id ? $this->display_action_id : '', // 開いているタブのアクションID
            'brand_id'          => $this->getBrand()->id,
            'limit'             => $this->limit,
            'join_user'         => $this->join_user
        );
    }

    public function validate() {

        if (!$this->Data['list_page']['limit'] || !in_array($this->Data['list_page']['limit'], CpCreateSqlService::$display_items_range)) {
            $this->Data['list_page']['limit'] = CpCreateSqlService::DISPLAY_50_ITEMS;
        }

        $validatorService = new CpValidator($this->Data['list_page']['brand_id']);
        if (!$validatorService->isOwner($this->Data['list_page']['cp_id'])) {
            return false;
        }
        if (!$validatorService->isOwnerOfAction($this->Data['list_page']['action_id'])) {
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $cp_action = $cp_flow_service->getCpActionById($this->action_id);
        $action_group = $cp_flow_service->getCpActionGroupById($cp_action->cp_action_group_id);

        if($this->tab_no) {
            $this->Data['list_page']['tab_no'] = $this->tab_no;
        } elseif($action_group->order_no == 1) {
            $this->Data['list_page']['tab_no'] = CpCreateSqlService::TAB_PAGE_PROFILE;
        } else {
            $this->Data['list_page']['tab_no'] = CpCreateSqlService::TAB_PAGE_PARTICIPATE_CONDITION;
        }

        $search_condition = $this->getSearchConditionSession($this->cp_id);

        $cp = $cp_flow_service->getCpById($this->cp_id);

        // 参加者一覧を開いた時のデフォルトの絞り込み
        // 参加者一覧ボタンから遷移
        //   最初のアクションの通過者
        // 第1グループ:
        //   通常キャンペーン:最初のアクションの通過者
        //   限定キャンペーン:絞り込みなし
        //   メッセージ     :絞り込みなし
        // 第2グループ以降:
        //   第1グループの最後のアクションの完了者
        if(!$this->shift_in_page && $action_group->order_no > 1) {
            $first_group = $cp_flow_service->getCpActionGroupByCpIdAndOrderNo($this->Data['list_page']['cp_id'], 1);
            $first_group_last_action = $cp_flow_service->getMaxStepNo($first_group->id);
            $search_condition[CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION . '/' . $first_group_last_action->id]['search_participate_condition/' . $first_group_last_action->id . '/' . CpCreateSqlService::PARTICIPATE_COMPLETE] = CpCreateSqlService::PARTICIPATE_COMPLETE;
        } elseif(!$this->shift_in_page && $cp->type == Cp::TYPE_CAMPAIGN &&
            ($cp->join_limit_flg == Cp::JOIN_LIMIT_OFF || ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON && $this->Data['list_page']['join_user']))) {
            $search_condition[CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION . '/' . $cp_action->id]['search_participate_condition/' . $cp_action->id . '/' . CpCreateSqlService::PARTICIPATE_COMPLETE] = CpCreateSqlService::PARTICIPATE_COMPLETE;
        }

        if(!$this->query_flg) {
            if ($this->query_user == CpCreateSqlService::QUERY_USER_TARGET) {
                /** @var  CpMessageDeliveryService $message_delivery_service */
                $message_delivery_service = $this->createService('CpMessageDeliveryService');
                $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->Data['list_page']['brand_id'], $this->action_id);
                $this->Data['list_page']['reservation_id'] = $reservation->id;

                $search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] = CpCreateSqlService::QUERY_USER_TARGET . '/' . $reservation->id . '/' . $this->action_id;
            } elseif ($this->query_user == CpCreateSqlService::QUERY_USER_SENT) {
                $search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] = CpCreateSqlService::QUERY_USER_SENT . '/' . $this->action_id;
            } elseif ($this->query_user == CpCreateSqlService::QUERY_USER_ALL) {
                unset($search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE]);
            }
        }

        //ファンリストのオプションを持っている時、またはmanagerの時に全ファンを出す
        if($this->getBrand()->hasOption(BrandOptions::OPTION_FAN_LIST)) {
            $search_condition[CpCreateSqlService::SEARCH_JOIN_FAN_ONLY] = 0;
        } else if($this->getManager()->id) {
            $search_condition[CpCreateSqlService::SEARCH_JOIN_FAN_ONLY] = 0;
        } else {
            $search_condition[CpCreateSqlService::SEARCH_JOIN_FAN_ONLY] = 1;
        }

        //Segment Condition排除
        unset($search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION]);

        $this->setSearchConditionSession($this->cp_id,$search_condition);
        $order_condition = $this->getBrandSession('orderCondition');

        //Type Message: Add Segment Condition
        $segment_condition_session = null;
        $show_segment_condition = false;

        if($cp->isCpTypeMessage()) {

            /** @var SegmentService $segment_service */
            $segment_service = $this->createService('SegmentService');

            //Update Segment Condition Session
            $segment_condition_session = json_decode($this->segment_condition_session, true);
            $this->setBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY, $segment_condition_session);

            $provision_ids = $segment_service->getProvisionIdsFromSession($segment_condition_session);

            if($provision_ids) {

                $previous_date = strtotime('yesterday');
                $cur_date = strtotime('today');

                $create_date_array = array($previous_date, $cur_date);

                $search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION] = array(
                    'create_dates' => $create_date_array,
                    'provision_ids' => $provision_ids,
                );
            }

            $show_segment_condition = true;
        }

        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->createService('CpUserListService');
        $fan_list = $cp_user_list_service->getDisplayFanListAndCount($this->Data['list_page'], $search_condition, $order_condition);

        $html = $this->sanitizeOutput(aafwWidgets::getInstance()->loadWidget($this->getBrand()->id == 375 ? 'RecruitCpUserList' : 'CpUserList')->render(array(
            'brand' => $this->getBrand(),
            'fan_list_users' => $fan_list,
            'list_page' => $this->Data['list_page'],
            'search_condition' => $search_condition,
            'show_sent_time' => $this->getSearchConditionSession($this->cp_id)[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] == CpCreateSqlService::QUERY_USER_SENT . '/' . $this->action_id,
            'segment_condition_session' => $segment_condition_session,
            'show_segment_condition' => $show_segment_condition
        )));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function sanitizeOutput($html) {
        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );
        $html = preg_replace($search, $replace, $html);
        return $html;
    }
}
