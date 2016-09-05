<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');

class show_reservation_info extends BrandcoGETActionBase {

    protected $ContainerName = 'show_reservation_info';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['current_page'] = Cp::PAGE_SHOW_RESERVATION_INFO;
        $this->Data['action_id'] = $this->GET['exts'][0];

        $service_factory = new aafwServiceFactory();
        $cp_flow_service = $service_factory->create('CpFlowService');
        $this->action = $cp_flow_service->getCpActionById($this->Data['action_id']);
        $this->group_actions = $cp_flow_service->getCpActionsByCpActionGroupId($this->action->cp_action_group_id);
    }

    public function validate() {

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

        $this->Data['is_group_fixed'] = true;
        foreach ($this->Data['group_actions'] as $action) {
            if ($action->status == CpAction::STATUS_DRAFT) {
                $this->Data['is_group_fixed'] = false;
                break;
            }
        }

        $this->Data['is_include_type_announce'] = false;
        foreach ($this->group_actions as $action) {
            if($action->type === CpAction::TYPE_ANNOUNCE) {
                $this->Data['is_include_type_announce'] = true;
                break;
            }
        }

        $this->Data['loginInfo'] = $this->getLoginInfo();
        $this->Data['loginInfo']['brand'] = $this->Data['brand'];

        /** @var  CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->createService('CpMessageDeliveryService');

        $cp_acton_group = $this->action->getCpActionGroup();
        $cp = $cp_acton_group->getCp();

        // Reservationを取得
        $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->action->id);


        if (!$reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "edit_action", array("cp_id" => $cp->id, "action_id" => $this->action->id), array("mid" => $this->GET['mid']));
        }


        $targets_count = $message_delivery_service->getTargetsCount($this->Data['brand']->id, $reservation->id);

        $this->Data['cp_id'] = $cp->id;
        $this->Data['reservation'] = $reservation;
        $this->Data['targets_count'] = $targets_count;

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($reservation->id);
        $this->Data['fixed_target'] = $fixed_target ? true : false;

        return 'user/brandco/admin-cp/show_reservation_info.php';
    }
}
