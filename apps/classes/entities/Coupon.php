<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
AAFW::import ( 'jp.aainc.classes.brandco.cp.interface.CpCreator' );
/**
 * @property mixed directory_name
 */
class Coupon extends aafwEntityBase {

    const DISTRIBUTION_TYPE_RANDOM = 0;
    const DISTRIBUTION_TYPE_REGISTER_ORDER = 1;

    public static $distribution_type_label = array(
        self::DISTRIBUTION_TYPE_RANDOM          => 'ランダム',
        self::DISTRIBUTION_TYPE_REGISTER_ORDER  => '登録順'
    );

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        )
    );

    /**
     * @return int|mixed
     */
    public function countReservedNum() {
        $coupon_action_manager = new CpCouponActionManager();
        $coupon_actions_concrete = $coupon_action_manager->getCpCouponActionsByCouponId($this->id);

        $reserved_num = 0;
        foreach ($coupon_actions_concrete as $coupon_action_concrete) {
            $reserved_num += $this->countReservedNumByCouponAction($coupon_action_concrete);
        }
        return $reserved_num;
    }

    public function countReservedNumByCouponAction($coupon_action_concrete) {
        if ($coupon_action_concrete->coupon_id != $this->id) {
            return 0;
        }

        $coupon_action_manager = new CpCouponActionManager();

        $cp_action = $coupon_action_manager->getCpActionById($coupon_action_concrete->cp_action_id);
        //キャンペーンで一番グループ
        $cp = $cp_action->getCp();
        if ($cp->type == Cp::TYPE_CAMPAIGN &&  $cp_action->getCpActionGroup()->order_no == 1 ) {
            //限定じゃないパターンあるいは限定と先着順パターン
            if ($cp->join_limit_flg != Cp::JOIN_LIMIT_ON
                || ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON && $cp->selection_method == CpCreator::ANNOUNCE_FIRST)) {

                return $cp->winner_count;

            } else {
                return $this->countReservedMessageAction($cp_action);
            }

        } else {
            return $this->countReservedMessageAction($cp_action);
        }
    }

    private function countReservedMessageAction($cp_action) {
        $service_factory = new aafwServiceFactory();
        /** @var CpMessageDeliveryService $delivery_service */
        $delivery_service = $service_factory->create('CpMessageDeliveryService');
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');

        $cp_first_action = $cp_flow_service->getFirstActionInGroupByAction($cp_action);
        $message_delivery_reservation = $delivery_service->getCpMessageDeliveryReservationsByCpActionId($cp_first_action->id);
        $message_count = 0;
        foreach ($message_delivery_reservation as $message) {
            if ($message->status <= CpMessageDeliveryReservation::STATUS_FIX) {
                continue;
            }
            $message_count += $delivery_service->getCpMessageDeliveryTargetsCountByReservationId($message->id);
        }
        return $message_count;
    }

    public function countReservedByCpId($cp_id) {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $coupon_action_manager = new CpCouponActionManager();
        $cp_actions = $cp_flow_service->getCpActionsByCpId($cp_id);
        $reserved_num = 0;
        foreach ($cp_actions as $cp_action) {
            if ($cp_action->type != CpAction::TYPE_COUPON) {
                continue;
            }
            $concrete_action = $coupon_action_manager->getConcreteAction($cp_action);
            if ($concrete_action->coupon_id != $this->id) {
                continue;
            }
            $reserved_num += $this->countReservedNumByCouponAction($concrete_action);
        }
        return $reserved_num;
    }
}
