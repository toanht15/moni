<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');

class CPMessageReservationMailOptionValidator extends BaseValidator {

    private $brand_id;
    private $reservation_id;
    private $delivery_type;
    private $delivery_time;
    private $send_mail_flg;
    private $status;

    private $service_factory;
    private $reservation;
    private $cp_action;


    public function __construct($brand_id, $reservation_id, $delivery_type, $delivery_time, $send_mail_flg, $status) {
        parent::__construct();
        $this->brand_id = $brand_id;
        $this->reservation_id = $reservation_id;
        $this->delivery_type = $delivery_type;
        $this->delivery_time = $delivery_time;
        $this->send_mail_flg = $send_mail_flg;
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
        $cp = $cp_acton_group->getCp();

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

        // $delivery_typeのチェック
        if (!in_array($this->delivery_type, CpMessageDeliveryReservation::$delivery_type_array)) {
            $this->errors['delivery_type'] = "RESERVATION_INVALID_DELIVERY_TYPE";
            return;
        }

        // $delivery_timeのチェック
        if ($this->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_RESERVATION) {
            if (!$this->isCorrectDate($this->delivery_time)) {
                $this->errors['delivery_date'] = 'RESERVATION_INVALID_DELIVERY_TIME';
                return;
            }
        }

        //一番初めのアクションの時はキャンペーンの開催期間中に配信されているかを確認する
        if($cp->type == cp::TYPE_CAMPAIGN && $cp->status != Cp::STATUS_DEMO) {
            /** @var CpFlowService $cp_service */
            $cp_service = $this->service_factory->create('CpFlowService');
            $first_action = $cp_service->getFirstActionOfCp($cp->id);
            if($cp_action->id == $first_action->id) {
                if ($this->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_RESERVATION) {
                    $delivery_time = $this->delivery_time;
                } else if ($this->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY) {
                    $delivery_time = date("Y/m/d H:i:s");
                }

                if (strtotime($delivery_time) <= strtotime($cp->public_date)) {
                    $this->errors['delivery_date'] = 'RESERVATION_INVALID_DELIVERY_TIME_LIMIT';
                    return;
                }

                if (!$cp->isPermanent() && strtotime($delivery_time) >= strtotime($cp->end_date)) {
                    $this->errors['delivery_date'] = 'RESERVATION_INVALID_DELIVERY_TIME_LIMIT';
                    return;
                }
            }
        }

        // $send_mail_flgのチェック
        if (!in_array($this->send_mail_flg, CpMessageDeliveryReservation::$send_mail_flg_array)) {
            $this->errors['send_mail_flg'] = "RESERVATION_INVALID_SEND_MAIL_FLG";
            return;
        }

        // 送信されたステータスのチェック
        if (!in_array($this->status, CpMessageDeliveryReservation::$change_status_array)) {
            $this->errors['reservation_id'] = "RESERVATION_INVALID_DELIVERY_STATUS";
            return;
        }

        $this->cp_action = $cp_action;
    }

    /**
     * @param $date
     * @return bool
     */
    private function isCorrectDate($date) {
        if ($this->validator->isEmpty($date)) {
            return false;
        }
        $now = date("Y/m/d H:i:s");
        $now = DateTime::createFromFormat("Y/m/d H:i:s", $now);
        $date = DateTime::createFromFormat("Y/m/d H:i:s", $date);
        if (!$now || !$date) {
            return false;
        }
        if ($now > $date) {
            return false;
        }
        return true;
    }

    public function getCleanedData() {
        return [
            "cp_action" => $this->cp_action,
            "reservation" => $this->reservation,
            "delivery_type" => $this->delivery_type,
            "delivery_time" => $this->delivery_time,
            "send_mail_flg" => $this->send_mail_flg,
            "status" => $this->status,
        ];
    }

}
