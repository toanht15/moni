<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionCoupon extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        /** @var CouponService $coupon_service */
        $coupon_service = $service_factory->create('CouponService');
        $coupon_manager = new CpCouponActionManager();
        $cp_coupon_action = $coupon_manager->getConcreteAction($params['action']);
        if ($cp_coupon_action) {
            $params['current_coupon'] = $coupon_service->getCouponById($cp_coupon_action->coupon_id);
        }

        $cp_action_groups = $params['action']->getCpActionGroups();
        $cur_cp_action_group = $cp_action_groups->current();

        if ($cp->selection_method != CpCreator::ANNOUNCE_FIRST || $cur_cp_action_group->order_no != 1) {
            $params['coupons'] = $coupon_service->getAllCouponsByBrandId($cp->brand_id);
        } else {
            $params['coupons'] = $coupon_service->getCpCouponByBrandIdAndWinnerCount($cp->brand_id, $cp->winner_count);
        }

        return $params;
    }
} 