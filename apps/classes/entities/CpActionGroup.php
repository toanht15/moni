<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpActionGroup extends aafwEntityBase {

    protected $_Relations = array(
        'Cps' => array(
            'cp_id' => 'id',
        ),
        'CpActions' => array(
            'id' => 'cp_action_group_id'
        )
    );

    public function getStepName() {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $actions = $cp_flow_service->getCpActionsByCpActionGroupId($this->id);

        if (!$actions) {
            return "";
        }

        $total = $actions->total();

        $min_order = $cp_flow_service->getMinOrderOfActionInGroup($this->id) + 1;

        if ($total == 1) {
            return "STEP".$min_order;
        }

        return "STEP" . $min_order . "~" . ($min_order + $total - 1);
    }

    public function getCouponActions() {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        return $cp_flow_service->getCpActionsByCpActionGroupIdAndType($this->id, CpAction::TYPE_COUPON);
    }

    public function getAnnounceActions() {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        return $cp_flow_service->getCpActionsByCpActionGroupIdAndType($this->id, CpAction::TYPE_ANNOUNCE);
    }

    /**
     * 最初のステップグループであるか
     *
     * @return boolean
     */
    public function isFirstGroup() {
        return $this->order_no == 1;
    }
}
