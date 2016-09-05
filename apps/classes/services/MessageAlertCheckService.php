<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
class MessageAlertCheckService extends aafwServiceBase {

    private $message_alert_checks;

    public function __construct() {
        $this->message_alert_checks = $this->getModel('MessageAlertChecks');
    }

    public function addMessageAlertCheck($reservation_id, $cp_id, $checked) {
        $messageAlertCheck = $this->message_alert_checks->createEmptyObject();
        $messageAlertCheck->cp_message_delivery_reservation_id = $reservation_id;
        $messageAlertCheck->cp_id = $cp_id;
        $messageAlertCheck->checked = $checked;
        $messageAlertCheck->count = $checked ? 0 : 1;
        return $this->message_alert_checks->save($messageAlertCheck);
    }

    public function updateMessageAlertCheck($messageAlertCheck) {
        $this->message_alert_checks->save($messageAlertCheck);
    }

    public function getMessageAlertCheck($reservation_id) {
        $filter = array(
            'cp_message_delivery_reservation_id' => $reservation_id,
        );
        return $this->message_alert_checks->findOne($filter);
    }

    public function getListMessageAlertCheck($status) {
        $filter = array(
            'status' => $status,
        );
        return $this->message_alert_checks->find($filter);
    }

    public function getSendMailAlertCheck($cp_id) {
        $filter = array(
            'cp_message_delivery_reservation_id' => 0,
            'cp_id' => $cp_id,
        );
        return $this->message_alert_checks->findOne($filter);
    }

}