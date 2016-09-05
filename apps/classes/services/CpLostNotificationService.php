<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * 落選者通知
 * Class CpLostNotificationService
 */
class CpLostNotificationService extends aafwServiceBase {
    private $cp_lost_notification_store;

    public function __construct() {
        $this->cp_lost_notification_store = $this->getModel('CpLostNotifications');
    }

    /**
     * @param $params
     */
    public function updateCpLostNotification($params) {
        $cp_lost_notification = $this->getCpLostNotificationByCpActionId($params['cp_action_id']);
        if(!$cp_lost_notification){
            $cp_lost_notification = $this->cp_lost_notification_store->createEmptyObject();
            $cp_lost_notification->cp_action_id = $params['cp_action_id'];
        }
        $cp_lost_notification->notified = $params['notified'];

        return $this->cp_lost_notification_store->save($cp_lost_notification);
    }

    /**
     * @param $cp_action_id
     */
    public function getCpLostNotificationByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->cp_lost_notification_store->findOne($filter);
    }
}