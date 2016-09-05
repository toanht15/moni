<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpLostNotificationUserService extends aafwServiceBase {
    private $cp_lost_notification_user_store;

    public function __construct() {
        $this->cp_lost_notification_user_store = $this->getModel('CpLostNotificationUsers');
    }

    /**
     * @param $params
     */
    public function createCpLostNotificationUser($params) {
        $lost_notification_user = $this->cp_lost_notification_user_store->createEmptyObject();
        $lost_notification_user->cp_lost_notification_id = $params['cp_lost_notification_id'];
        $lost_notification_user->user_id = $params['user_id'];
        $this->cp_lost_notification_user_store->save($lost_notification_user);
    }

    /**
     * @param $lost_notification_user
     */
    public function updateCpLostNotificationUser($lost_notification_user) {
        $this->cp_lost_notification_user_store->save($lost_notification_user);
    }

    /**
     * @param $user_id
     */
    public function getCpLostNotificationUserByUserId($user_id){
        $filter = array(
            'user_id' => $user_id
        );

        return $this->cp_lost_notification_user_store->find($filter);
    }

}