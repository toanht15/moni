<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpUserService');
AAFW::import('jp.aainc.classes.services.SegmentService');

class api_change_reservation_status extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_change_message_option_status';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    /** @var  CPMessageReservationChangeStatusValidator $reservation_validator*/
    private $reservation_validator;

    public function doThisFirst() {
        $this->setBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY, null);
    }

    public function validate() {

        $brand = $this->getBrand();
        $this->Data["reservation_id"] = $this->POST['reservation_id'];
        $this->Data["status"] = $this->POST['status'];
        $this->Data["delivery_type"] = $this->POST['delivery_type'];

        $this->reservation_validator = new CPMessageReservationChangeStatusValidator($brand->id, $this->Data["reservation_id"], $this->Data["status"]);
        $this->reservation_validator->validate();

        if (!$this->reservation_validator->isValid()) {

            // アクションのValidatorに詰め替え
            $errors = $this->reservation_validator->getErrors();

            $keys = ["cp_id", "reservation_id"];
            foreach ($keys as $key) {
                if (array_key_exists($key, $errors)) {
                    return 404;
                }
            }
            return false;
        }

        return true;
    }

    function doAction() {

        if ($this->reservation_validator->message['coupon']) {
            $json_data = $this->createAjaxResponse("ng", array(), array('message'=>$this->reservation_validator->message['coupon']));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        /** @var CpMessageDeliveryService $delivery_service */
        $delivery_service = $this->createService('CpMessageDeliveryService');

        $cleaned_data = $this->reservation_validator->getCleanedData();
        $reservation = $cleaned_data["reservation"];

        $reservation->status = $cleaned_data["status"];

        $reservation_store = $delivery_service->getReservationStore();
        try {
            $reservation_store->begin();

            if ($this->Data['delivery_type'] == CpMessageDeliveryReservation::DELIVERY_TYPE_NONE) {
                $reservation->delivery_type = CpMessageDeliveryReservation::DELIVERY_TYPE_NONE;
                $reservation->delivery_date = CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND;
            }

            $delivery_service->updateCpMessageDeliveryReservation($reservation);

            //クーポン割当数更新
            if ($cleaned_data['coupon_reserved_array'] && count($cleaned_data['coupon_reserved_array']) > 0) {
                /** @var CouponService $coupon_service */
                $coupon_service = $this->createService('CouponService');
                foreach ($cleaned_data['coupon_reserved_array'] as $key => $value) {
                    $coupon = $coupon_service->getCouponById($key);
                    $coupon->reserved_num = $value;
                    $coupon_service->saveCoupon($coupon);
                }
            }
            $reservation_store->commit();

        } catch (Exception $e) {
            $reservation_store->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}