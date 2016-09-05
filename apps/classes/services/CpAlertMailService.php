<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class CpAlertMailService extends aafwServiceBase {
    protected $manager;

    const NOW_ANNOUNCE_FLG_SUBSCRIBE = "0"; //配信中
    const NOW_ANNOUNCE_FLG_UNSUBSCRIBE = "1"; //非配信中

    const PASSED_ANNOUNCE_FLG_SUBSCRIBE = "0"; //配信中
    const PASSED_ANNOUNCE_FLG_UNSUBSCRIBE = "1"; //非配信中

    public function __construct() {
        $this->cp_alert_mails = $this->getModel('CpAlertMails');
    }

    public function getCpAlertMailsByCpId($cpId) {
        $filter = array(
            'conditions' => array(
                'cp_id' => $cpId
            )
        );
        return $this->cp_alert_mails->findOne($filter);
    }
}