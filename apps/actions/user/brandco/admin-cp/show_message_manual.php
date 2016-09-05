<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');

class show_message_manual extends BrandcoGETActionBase {
    protected $ContainerName = 'show_message_manual';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $action;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['action_id'] = $this->GET['exts'][1];

        $service_factory = new aafwServiceFactory();
        $cp_flow_service = $service_factory->create('CpFlowService');
        $this->action = $cp_flow_service->getCpActionById($this->Data['action_id']);
        $this->Data['group_actions'] = $cp_flow_service->getCpActionsByCpActionGroupId($this->action->cp_action_group_id);
    }

    public function validate() {
        $brand = $this->getBrand();
        $cp_validator = new CpValidator($brand->id);
        if (!($this->action->type == CpAction::TYPE_ANNOUNCE || $this->action->type == CpAction::TYPE_ANNOUNCE_DELIVERY)) {
            return false;
        }

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

        if ($this->action->order_no == 1) {
            $first_action_id = $this->Data['action_id'];
        } else {
            $first_action = $this->Data['group_actions']->current();
            $first_action_id = $first_action->id;
        }

        $this->Data['loginInfo'] = $this->getLoginInfo();
        $this->Data['loginInfo']['brand'] = $this->Data['brand'];

        $this->deleteErrorSession();

        /** @var CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->createService('CpMessageDeliveryService');
        $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->Data['action_id']);

        if ($reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "show_reservation_info", array("action_id" => $first_action_id));
        }

        $this->Data["reservation"] = $reservation;

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($reservation->id);
        $this->Data['fixed_target'] = $fixed_target ? true : false;

        return 'user/brandco/admin-cp/show_message_manual.php';
    }
}
