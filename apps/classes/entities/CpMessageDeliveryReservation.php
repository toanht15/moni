<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.CpAction');

class CpMessageDeliveryReservation extends aafwEntityBase {

    const STATUS_DRAFT          = "1"; // 下書き
    const STATUS_FIX            = "2"; // 確定
    const STATUS_SCHEDULED      = "3"; // 予約済み
    const STATUS_DELIVERING     = "4"; // 配信中
    const STATUS_DELIVERED      = "5"; // 配信済み
    const STATUS_DELIVERY_FAIL  = "6"; // 配信失敗

    const DELIVERY_TYPE_IMMEDIATELY = "1"; // 即時配信
    const DELIVERY_TYPE_RESERVATION = "2"; // 予約配信
    const DELIVERY_TYPE_NONE        = "3"; // 配信しない

    const DELIVERY_DATE_NOT_SEND = '9999-12-31 23:59:59'; // 発送をもってモードではメールを送らないので念の為日付をMAXに設定しておく

    const SEND_MAIL_ON  = "1";
    const SEND_MAIL_OFF = "2";

    const TYPE_ALL      = "1"; // すべて
    const TYPE_IDS      = "2"; // ID指定
    const TYPE_SEARCH   = "3"; // 検索

    const MONIPLA_STATUS_SCHEDULED      = "0";
    const MONIPLA_STATUS_UPDATED        = "1";
    const MONIPLA_STATUS_UPDATE_FAILED  = "2";
    const MONIPLA_STATUS_SKIP = "3";


    protected $_Relations = array(

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );


    public static $change_status_array = [
        self::STATUS_DRAFT,
        self::STATUS_FIX,
        self::STATUS_SCHEDULED
    ];

    public static $can_change_status_array = [
        self::STATUS_DRAFT,
        self::STATUS_FIX,
        self::STATUS_SCHEDULED
    ];

    public static $delivery_type_array = [
        self::DELIVERY_TYPE_IMMEDIATELY,
        self::DELIVERY_TYPE_RESERVATION,
    ];

    public static $send_mail_flg_array = [
        self::SEND_MAIL_ON,
        self::SEND_MAIL_OFF,
    ];

    public static $send_mail_flg_string = [
        self::SEND_MAIL_ON => "メール通知あり",
        self::SEND_MAIL_OFF => "メール通知なし"
    ];

    public static $delivery_type_string = [
        self::DELIVERY_TYPE_IMMEDIATELY => "即時配信",
        self::DELIVERY_TYPE_RESERVATION => "予約配信"
    ];


    /**
     * @return mixed
     */
    public function sendMailFlgString() {
        return self::$send_mail_flg_string[$this->send_mail_flg];
    }

    /**
     * @return mixed
     */
    public function deliveryTypeString() {
        return self::$delivery_type_string[$this->delivery_type];
    }

    /**
     * @return true
     */
    public function deliveryDateString() {
        if ($this->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY) {
            $delivery_date = date('Y-m-d H:i:s');
        } else {
            $delivery_date = $this->delivery_date;
        }

        return $delivery_date;
    }

    /**
     * @return bool
     */
    public function canEdit() {
        if ($this->status == self::STATUS_DRAFT) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isScheduled() {
        if ($this->status == self::STATUS_SCHEDULED) {
            return true;
        }
        return false;
    }

    public function isFixedAnnounceDeliveryUser() {
        return ($this->delivery_type == self::DELIVERY_TYPE_NONE && $this->status == self::STATUS_DELIVERED && $this->delivery_date == self::DELIVERY_DATE_NOT_SEND);
    }

    /**
     * @return bool
     */
    public function canSchedule() {
        $action = $this->getCpAction();
        if ($action->status == CpAction::STATUS_FIX) {
            if ($this->status == self::STATUS_FIX) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isOverScheduled() {
        if ($this->status >= self::STATUS_SCHEDULED) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isDelivering() {
        if ($this->status == self::STATUS_DELIVERING) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isFailedDelivering() {
        if ($this->status == self::STATUS_DELIVERY_FAIL) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSendMail() {
        if($this->send_mail_flg == self::SEND_MAIL_ON) {
            return true;
        }
        return false;
    }
}
