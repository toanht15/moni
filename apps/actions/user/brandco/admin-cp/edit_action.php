<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.services.SegmentService');

class edit_action extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_action';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;
    /** @var CpFlowService $cp_flow_service */
    public $cp_flow_service;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['action_id'] = $this->GET['exts'][1];
        $this->Data['current_page'] = Cp::PAGE_EDIT_MESSAGE;
        $this->Data['brand'] = $this->getBrand();
        $in_action = $this->GET['in_action'] ? $this->GET['in_action'] : '';

        $service_factory = new aafwServiceFactory();

        $this->cp_flow_service = $service_factory->create('CpFlowService');
        if ($this->Data['action_id'] !== null) {
            $action = $this->cp_flow_service->getCpActionById($this->Data['action_id']);
        }

        // 当選発表の時はマニュアルのページへ(ただし、同じアクション内の遷移では行かない)
        if ($action === null) {
            $msg = "action_id is null!: " . json_encode($this->GET);
            aafwLog4phpLogger::getDefaultLogger()->warn($msg);
            aafwLog4phpLogger::getHipChatLogger()->warn($msg);
        }

        if ($action->type == CpAction::TYPE_ANNOUNCE && !$in_action) {
            $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
            $brand_global_settings = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::HIDE_FAN_LIST_MESSAGE_MANUAL_KEY);
            if(!$brand_global_settings || $brand_global_settings->content == BrandGlobalSettingService::VIEW_FAN_LIST_MESSAGE_MANUAL) {
                return 'redirect: '.Util::rewriteUrl('admin-cp', 'show_message_manual', array($this->Data['cp_id'], $this->Data['action_id']), array("mid" => $this->GET['mid']));
            }
        }

        if ($action->type == CpAction::TYPE_ANNOUNCE_DELIVERY && !$in_action) {
            $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
            $brand_global_settings = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL_KEY);
            if(!$brand_global_settings || $brand_global_settings->content == BrandGlobalSettingService::VIEW_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL) {
                return 'redirect: '.Util::rewriteUrl('admin-cp', 'show_message_manual', array($this->Data['cp_id'], $this->Data['action_id']), array("mid" => $this->GET['mid']));
            }
        }

        // 発送をもってモードの時はカレントページは配送者一覧
        if ($action && $action->isAnnounceDelivery()) {
            return 'redirect:' . Util::rewriteUrl('admin-cp', 'show_user_list', array($this->Data['cp_id'], $this->Data['action_id']));
        }
    }

    public function validate() {
        $brand = $this->getBrand();

        $cp = $this->cp_flow_service->getCpById($this->Data['cp_id']);
        if ($cp->status == Cp::STATUS_DRAFT) {
            return '404';
        }

        $cp_validator = new CpValidator($brand->id);

        if (!$cp_validator->isOwner($this->Data['cp_id'])) {
            return false;
        }

        if (!$cp_validator->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }
        if (!$cp_validator->isIncludedInCp($this->Data['cp_id'], $this->Data['action_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {

        $this->Data['action'] = $this->cp_flow_service->getCpActionById($this->Data['action_id']);

        $this->Data['action_type'] = $this->Data['action']->type;

        $this->Data['min_step_no'] = $this->cp_flow_service->getMinOrderOfActionInGroup($this->Data['action']->cp_action_group_id);

        $cp_action_detail = $this->Data['action']->getCpActionDetail();
        $this->ContainerName = $cp_action_detail['form_action'];

        $this->deleteErrorSession();

        $action_manager = $this->Data['action']->getActionManagerClass();

        list($this->Data['action'], $concrete_action) = $action_manager->getCpActions($this->Data['action_id']);

        $this->Data['group_actions'] = $this->cp_flow_service->getCpActionsByCpActionGroupId($this->Data['action']->cp_action_group_id);

        if ($this->Data['action']->order_no == 1) {
            $this->Data['first_action_id'] = $this->Data['action']->id;
        } else {
            $first_action = $this->Data['group_actions']->current();
            $this->Data['first_action_id'] = $first_action->id;
        }

        $this->Data['is_group_fixed'] = true;
        foreach ($this->Data['group_actions'] as $action) {
            if ($action->status == CpAction::STATUS_DRAFT) {
                $this->Data['is_group_fixed'] = false;
                break;
            }
        }

        $this->Data['is_include_type_announce'] = false;
        foreach ($this->Data['group_actions'] as $action) {
            if($action->type === CpAction::TYPE_ANNOUNCE) {
                $this->Data['is_include_type_announce'] = true;
                break;
            }
        }

        /** @var  CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->createService('CpMessageDeliveryService');
        $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->Data['first_action_id']);

        if ($reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "show_reservation_info", array("action_id" => $this->Data['first_action_id']), array("mid" => $this->GET['mid']));
        }

        $this->assign('ActionForm', $concrete_action->toArray());

        $this->Data['action'] = $concrete_action;
        $this->Data['reservation'] = $reservation;

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($reservation->id);
        $this->Data['fixed_target'] = $fixed_target ? true : false;

        //セグメントメッセージアクションを設定するかどうか
        $cp = $this->cp_flow_service->getCpById($this->Data['cp_id']);
        if($cp->type == Cp::TYPE_MESSAGE) {
            $this->Data['show_segment_message_action_alert'] = $this->getBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY) ? true : false;
        }

        return 'user/brandco/admin-cp/edit_action.php';
    }
}
