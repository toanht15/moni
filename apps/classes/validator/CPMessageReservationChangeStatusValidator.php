<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import ( 'jp.aainc.classes.brandco.cp.interface.CpCreator' );

class CPMessageReservationChangeStatusValidator extends BaseValidator {

    private $brand_id;
    private $reservation_id;
    private $service_factory;
    private $reservation;
    private $cp_action;

    public $message;
    private $coupon_reserved_array;

    public function __construct($brand_id, $reservation_id, $status) {
        parent::__construct();
        $this->brand_id = $brand_id;
        $this->reservation_id = $reservation_id;
        $this->status = $status;
        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {

        // 予約情報の取得
        /** @var CpMessageDeliveryService $delivery_service */
        $delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $this->reservation = $delivery_service->getCpMessageDeliveryReservationById($this->reservation_id);

        if (!$this->reservation->id) {
            $this->errors['reservation_id'] = "RESERVATION_INVALID_NOT_EXIST";
            return;
        }

        // 現在のステータスのチェック(4以上は変更できない）
        if (!in_array($this->reservation->status, CpMessageDeliveryReservation::$can_change_status_array)) {
            $this->errors['reservation_id'] = "RESERVATION_INVALID_CURRENT_DELIVERY_STATUS";
            return;
        }

        $cp_action = $this->reservation->getCpAction();
        $cp_acton_group = $cp_action->getCpActionGroup();
        $coupon_actions = $cp_acton_group->getCouponActions();
        $cp = $cp_acton_group->getCp();
        if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON && $cp->selection_method == CpCreator::ANNOUNCE_FIRST && $cp_acton_group->order_no == 1) {
            //限定の先着順キャンペーンのグループ１だったらメールを送信するときにクーポン数の確認は必要ではない
        } else if ($coupon_actions) {
            $service_factory = new aafwServiceFactory();
            /** @var CpMessageDeliveryService message_delivery_service */
            $message_delivery_service = $service_factory->create('CpMessageDeliveryService');
            $target = $message_delivery_service->getCpMessageDeliveryTargetsByReservationId($this->reservation_id);
            /** @var CouponService $coupon_service */
            $coupon_service = $service_factory->create('CouponService');
            $coupon_action_manager = new CpCouponActionManager();
            $coupon_reserved_array = array();
            foreach ($coupon_actions as $coupon_action){
                $coupon_concrete_action = $coupon_action_manager->getConcreteAction($coupon_action);
                $coupon = $coupon_service->getCouponById($coupon_concrete_action->coupon_id);
                list($reserved_coupon, $total_coupon) = $coupon_service->getCouponStatisticByCouponId($coupon_concrete_action->coupon_id);
                if (!$coupon_reserved_array[$coupon->id]) {
                    $coupon_reserved_array[$coupon->id] = $coupon->countReservedNum();
                }
                if ($this->status == CpMessageDeliveryReservation::STATUS_SCHEDULED) {
                    if (($total_coupon - $coupon_reserved_array[$coupon->id]) < $target->total()) {
                        $this->message['coupon'] = "NOT_ENOUGH_COUPON";
                    }
                    $coupon_reserved_array[$coupon->id] = $coupon_reserved_array[$coupon->id] + $target->total();

                } else if ($this->status == CpMessageDeliveryReservation::STATUS_FIX) {

                    $coupon_reserved_array[$coupon->id] = $coupon_reserved_array[$coupon->id] - $target->total();
                }
            }
            $this->coupon_reserved_array = $coupon_reserved_array;
        }

        // キャンペーンの存在チェック
        if (!$cp->id) {
            $this->errors['cp_id'] = "CP_INVALID_NOT_EXIST";
            return;
        }

        // 所有者情報をチェック
        if ($cp->brand_id != $this->brand_id) {
            $this->errors['cp_id'] = "CP_INVALID_NOT_OWNER";
            return;
        }

        // 送信されたステータスのチェック
        if (!in_array($this->status, CpMessageDeliveryReservation::$change_status_array)) {
            $this->errors['reservation_id'] = "RESERVATION_INVALID_DELIVERY_STATUS";
            return;
        }

        $this->cp_action = $cp_action;
    }

    public function getCleanedData() {
        return [
            "cp_action" => $this->cp_action,
            "reservation" => $this->reservation,
            "status" => $this->status,
            "coupon_reserved_array" => $this->coupon_reserved_array
        ];
    }
}
