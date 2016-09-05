<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');

class setting_message_option extends BrandcoGETActionBase {

    protected $ContainerName = 'setting_message_option';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;
    /** @var CpFlowService $cp_flow_service */
    public $cp_flow_service;

    public function doThisFirst() {
        $this->Data['current_page'] = Cp::PAGE_SETTING_OPTION;
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {

        $this->cp_flow_service = $this->createService('CpFlowService');
        $this->Data['action'] = $this->cp_flow_service->getCpActionById($this->Data['action_id']);
        $cp_action_group = $this->Data['action']->getCpActionGroup();
        $this->Data['cp'] = $cp_action_group->getCp();

        if ($this->Data['cp']->status == Cp::STATUS_DRAFT) {
            return "404";
        }

        $brand = $this->getBrand();
        $cp_validator = new CpValidator($brand->id);

        if (!$cp_validator->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }

        if (!$cp_validator->isFirstActionOfGroup($this->Data['action_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {

        $group_actions = $this->cp_flow_service->getCpActionsByCpActionGroupId($this->Data['action']->cp_action_group_id);

        if ($this->Data['action']->order_no == 1) {
            $this->Data['first_action_id'] = $this->Data['action']->id;
        } else {
            $first_action = $group_actions->current();
            $this->Data['first_action_id'] = $first_action->id;
        }

        $this->Data['is_group_fixed'] = true;
        foreach ($group_actions as $action) {
            if ($action->status == CpAction::STATUS_DRAFT) {
                $this->Data['is_group_fixed'] = false;
                break;
            }
        }

        $this->Data['is_include_type_announce'] = false;
        foreach ($group_actions as $action) {
            if($action->type === CpAction::TYPE_ANNOUNCE) {
                $this->Data['is_include_type_announce'] = true;
                break;
            }
        }

        $this->Data['loginInfo'] = $this->getLoginInfo();
        $this->Data['loginInfo']['brand'] = $this->Data['brand'];

        /** @var  CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->createService('CpMessageDeliveryService');

        //$this->deleteErrorSession();

        // Reservationのチェック
        $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->Data['first_action_id']);

        if ($reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "show_reservation_info", array("action_id" => $this->Data['first_action_id']), array("mid" => $this->GET['mid']));
        }

        $action_form = $reservation->toArray();

        if ($action_form['delivery_date'] == '0000-00-00 00:00:00') {
            $action_form['delivery_date'] = '';
        } else {
            $delivery_time = strtotime($action_form['delivery_date']);
            $action_form['delivery_date'] = date('Y/m/d', $delivery_time);
            $action_form['delivery_time_hh'] = date('H', $delivery_time);
            $action_form['delivery_time_mm'] = date('i', $delivery_time);
        }

        $this->Data['reservation'] = $reservation;
        $this->Data['ActionForm'] = $action_form;

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($reservation->id);
        $this->Data['fixed_target'] = $fixed_target ? true : false;

        return 'user/brandco/admin-cp/setting_message_option.php';
    }
}
