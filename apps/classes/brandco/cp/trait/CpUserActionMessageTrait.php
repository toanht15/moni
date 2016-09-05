<?php

AAFW::import('jp.aainc.classes.entities.CpUserActionMessage');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.CpAction');

trait CpUserActionMessageTrait {

    protected $cp_user_action_messages;

    /**
     * @param $id
     * @return mixed
     */
    public function getCpUserActionMessageById($id) {
        return $this->cp_user_action_messages->findOne($id);
    }

    /**
     * @param $cp_user_id
     * @return mixed
     */
    public function getCpUserActionMessagesByCpUserId($cp_user_id) {

        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
            )
        );
        return $this->cp_user_action_messages->find($filter);
    }

    /**
     * @param $cp_id
     * @return mixed
     */
    public function getCpUserActionMessagesByCpId($cp_id) {

        $filter = array(
            'conditions' => array(
                "cp_id" => $cp_id,
            ),
        );
        return $this->cp_user_action_messages->find($filter);
    }

    /**
     * @param $cp_user_id
     * @param $page
     * @param $count
     * @param $order
     * @return mixed
     */
    public function getCpUserActionMessagesByCpUserIdAndPageAndCountAndOrder($cp_user_id, $page, $count, $order) {

        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            ),
            'order' => array(
                $order
            )
        );

        return $this->cp_user_action_messages->find($filter);
    }

    /**
     * @param $cp_user_id
     * @param $asc
     * @param $ignore_modules
     * @return mixed
     */
    public function getAllCpUserActionMessagesByCpUserIdOrderByActionOrder($cp_user_id , $asc = true, $ignore_modules = array()) {
        $direction = $asc ? 'asc' : 'desc';
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
            ),
            'join' => array(
                'type' => 'INNER',
                'name' => 'cp_actions',
                'key' => array('cp_user_action_messages.cp_action_id' => 'cp_actions.id')
            ),
            'order' => 'cp_actions.cp_action_group_id '.$direction.', cp_actions.order_no '.$direction,
        );

        if ($ignore_modules) {
            $filter['conditions']['cp_actions.type:<>'] = $ignore_modules;
        }
        
        return $this->cp_user_action_messages->find($filter);
    }

    public function findCpUserActionMessageByCpUserId($cp_user_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
            ),
            'join' => array(
                'type' => 'INNER',
                'name' => 'cp_actions',
                'key' => array('cp_user_action_messages.cp_action_id' => 'cp_actions.id')
            ),
        );
        
        return $this->cp_user_action_messages->findOne($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action
     * @param $concrete_action
     * @param bool $read_flg
     * @return mixed
     */
    public function createCpUserActionMessage($cp_user_id, $cp_action, $concrete_action, $read_flg = false) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_SEND_MESSAGE, $cp_action->id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE, $cp_action->id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_action->id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE_PC, $cp_action->id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE_SP, $cp_action->id));

        $cp_user_action_messages = $this->cp_user_action_messages->createEmptyObject();
        $cp_user_action_messages->cp_user_id = $cp_user_id;
        $cp_user_action_messages->cp_action_id = $cp_action->id;
        $cp_user_action_messages->title = $concrete_action->title;
        $cp_user_action_messages->created_at = date( "Y-m-d H:i:s" );
        if($read_flg) {
            $cp_user_action_messages->read_flg = CpUserActionMessage::STATUS_READ;
        } else {
            $cp_user_action_messages->read_flg = CpUserActionMessage::STATUS_UNREAD;
        }

        if ($this->getCpUserActionMessagesByCpUserIdAndCpActionId($cp_user_id, $cp_action->id)) {
            throw new aafwException("Duplicate entry exists!: cp_user_id=" . $cp_user_id . ", cp_action_id=" . $cp_action->id);
        }
        $new_message = $this->cp_user_action_messages->save($cp_user_action_messages);

        $service_factory = new aafwServiceFactory();
        $this->distributeCoupon($cp_user_id, $cp_action, $concrete_action, $service_factory);


        if (!$read_flg) {
            /** @var CpUserService $cp_user_service */
            $cp_user_service = $service_factory->create('CpUserService');

            $cp_user = $cp_user_service->getCpUserById($cp_user_id);
            $brand_id = CpInfoContainer::getInstance()->getCpById($cp_action->getCpActionGroup()->cp_id)->brand_id;
            $cacheManager->resetNotificationCount($brand_id, $cp_user->user_id);
        }

        return $new_message;
    }

    /**
     * @param CpUserActionMessage $cp_user_action_message
     */
    public function updateCpUserActionMessage(CpUserActionMessage $cp_user_action_message) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        try {
            $cacheManager->beginBatch();
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_PC, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_SP, $cp_user_action_message->cp_action_id));
            $cacheManager->flushBatch();
        } catch(Exception $e) {
            // 念のため
            $cacheManager->resetBatch();
        }

        $this->cp_user_action_messages->save($cp_user_action_message);
    }

    /**
     * @param CpUserActionMessage $cp_user_action_message
     */
    public function deleteCpUserActionMessage(CpUserActionMessage $cp_user_action_message) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        try {
            $cacheManager->beginBatch();
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_SEND_MESSAGE, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_PC, $cp_user_action_message->cp_action_id));
            $cacheManager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_SP, $cp_user_action_message->cp_action_id));
            $cacheManager->flushBatch();
        } catch(Exception $e) {
            // 念のため
            $cacheManager->resetBatch();
        }

        $this->cp_user_action_messages->delete($cp_user_action_message);
    }

    public function deleteCpUserActionMessageByCpUser($cp_user_id) {
        $messages = $this->getCpUserActionMessagesByCpUserId($cp_user_id);
        foreach ($messages as $message) {
            $this->deleteCpUserActionMessage($message);
        }
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpUserActionMessagesByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {

        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_action_id' => $cp_action_id,
            )
        );

        return $this->cp_user_action_messages->findOne($filter);

    }

    /**
     * @param CpUserActionMessage $message
     */
    public function readCpUserActionMessage(CpUserActionMessage $message, $cp = null, $cp_user = null) {
        $message->read_flg = CpUserActionMessage::STATUS_READ;
        $this->updateCpUserActionMessage($message);

        //update notification count
        $service_factory = new aafwServiceFactory();
        if ($cp_user === null) {
            /** @var CpUserService $cp_user_service */
            $cp_user_service = $service_factory->create('CpUserService');
            $cp_user = $cp_user_service->getCpUserById($message->cp_user_id);
        }
        if ($cp === null) {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $service_factory->create('CpFlowService');
            $cp = $cp_flow_service->getCpById($cp_user->cp_id);
        }

        $cache_manager = new CacheManager();
        $notification_count = $cache_manager->getNotificationCount($cp->brand_id, $cp_user->user_id);
        if (!is_array($notification_count) || !$notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT]) {
            $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT] = 0;
        } else {
            $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT] -= 1;
        }
        $cache_manager->setNotificationCount(
            array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'),
                CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT]),
            $cp->brand_id, $cp_user->user_id);
    }

    /**
     * @param $messages
     */
    public function readCpUserActionMessages($messages, $cp = null, $cp_user = null) {
        foreach ($messages as $message) {
            if($message->read_flg == CpUserActionMessage::STATUS_UNREAD)
                $this->readCpUserActionMessage($message, $cp, $cp_user);
        }
    }

    /**
     * @param $cp_user_id
     * @return mixed
     */
    public function countUnreadActionByUserId($cp_user_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'read_flg' => CpUserActionMessage::STATUS_UNREAD
            )
        );
        return $this->cp_user_action_messages->count($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpUserActionMessagesByCpActionId($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            )
        );
        return $this->cp_user_action_messages->find($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action
     * @param $concrete_action
     * @param $service_factory
     * @return array
     */
    public function distributeCoupon($cp_user_id, $cp_action, $concrete_action, $service_factory) {
        // クーポンアクションだったらクーポンコードを配布する。
        if ($cp_action->type === CpAction::TYPE_COUPON) {
            $coupon_action_manager = new CpCouponActionManager();
            /** @var CouponService $coupon_service */
            $coupon_service = $service_factory->create('CouponService');
            /** @var CpUserService $cp_user_service */
            $cp_user_service = $service_factory->create('CpUserService');

            /** @var CpTransactionService $transaction_service */
            $transaction_service = $service_factory->create('CpTransactionService');

            $transaction_service->getCpTransactionByIdForUpdate($cp_action->id);

            $coupon_code = $coupon_service->getCouponCodeForDistribute($concrete_action->coupon_id);
            if ($coupon_code) {
                $cp_user = $cp_user_service->getCpUserById($cp_user_id);
                $coupon_action_manager->createCouponCodeUser($coupon_code, $cp_user->user_id, $cp_action->id);
            }
        }
    }

}
