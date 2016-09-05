<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpUserActionStatusTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpUserActionMessageTrait');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinUserService');

class CpUserActionStatusService extends aafwServiceBase {

    use CpActionTrait;
    use CpUserActionStatusTrait;
    use CpUserActionMessageTrait;

    protected $cache_manager;

    public function __construct() {
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_user_action_statuses = $this->getModel("CpUserActionStatuses");
        $this->cp_user_action_messages = $this->getModel("CpUserActionMessages");
    }

    public function getCacheManager() {
        if (!$this->cache_manager) {
            $this->cache_manager = new CacheManager();
        }
        return $this->cache_manager;
    }

    public function isExistedStatusByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );
        $db = aafwDataBuilder::newBuilder();
        //普通にselect count(*) するよりもパフォーマンスがいいのでこのSQLを使っています
        //TODO : aafwEntityStoreBaseに共通処理として切り出した方が
        $result = $db->existsCpUserActionStatus($filter, array(), null, false);
        return $result[0]['isExist'];
    }

    public function getStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $cp_user_action_status = $this->getCpUserActionStatus($cp_user_id, $cp_action_id);
        if($cp_user_action_status->status) {
            $status = $cp_user_action_status->status == CpUserActionStatus::CAN_NOT_JOIN ? CpUserActionStatus::STATUS_REJECTED : CpUserActionStatus::STATUS_FINISH;
        } else {
            $message = $this->getCpUserActionMessagesByCpUserIdAndCpActionId($cp_user_id, $cp_action_id);
            if(!$message) {
                $status = CpUserActionStatus::STATUS_UNSENT;
            } else {
                if(!$message->read_flg) {
                    $status = CpUserActionStatus::STATUS_UNREAD;
                } else {
                    $status = CpUserActionStatus::STATUS_READ;
                }
            }
        }
        return $status;
    }


    public function getInstantWinActionStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $cp_user_action_status = $this->getCpUserActionStatus($cp_user_id, $cp_action_id);
        /** @var InstantWinUserService $instant_win_user_service */
        $instant_win_user_service = $this->getService('InstantWinUserService');
        $instant_win_user = $instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id);
        if($cp_user_action_status->status) {
            $status = CpUserActionStatus::STATUS_WIN.'（参加数:' . $instant_win_user->join_count . ')';
        } else {
            $message = $this->getCpUserActionMessagesByCpUserIdAndCpActionId($cp_user_id, $cp_action_id);
            if(!$message) {
                $status = CpUserActionStatus::STATUS_UNSENT;
            } else {
                if(!$message->read_flg) {
                    $status = CpUserActionStatus::STATUS_UNREAD;
                } else {
                    $status = CpUserActionStatus::STATUS_LOSE.'（参加数:' . $instant_win_user->join_count . ')';
                }
            }
        }
        return $status;
    }

    public function getCouponActionStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $cp_user_action_status = $this->getCpUserActionStatus($cp_user_id, $cp_action_id);
        $service_factory = new aafwServiceFactory();
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $service_factory->create('CpUserService');
        $cp_user = $cp_user_service->getCpUserById($cp_user_id);

        $coupon_action_manager = new CpCouponActionManager();
        $coupon_code_user = $coupon_action_manager->getReservedCouponCodeUserByUserIdAndActionId($cp_user->user_id, $cp_action_id);

        if($cp_user_action_status->status && $coupon_code_user) {
            /** @var CouponService $coupon_service */
            $coupon_service = $service_factory->create('CouponService');
            $coupon_code = $coupon_service->getCouponCodeById($coupon_code_user->coupon_code_id);

            $status = CpUserActionStatus::STATUS_WIN.'（コード:' . $coupon_code->code . '）';
        } else {
            $status = $this->getStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id);
        }
        return $status;
    }

    public function getFreeAnswerStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $cp_user_action_status = $this->getCpUserActionStatus($cp_user_id, $cp_action_id);
        if($cp_user_action_status->status) {
            $free_answer_manager = new CpFreeAnswerActionManager();
            $free_answer = $free_answer_manager->getAnswerByUserAndQuestion($cp_user_id, $cp_action_id);
            $status = CpUserActionStatus::STATUS_FINISH . '（' . $free_answer->free_answer . '）';
        } else {
            $status = $this->getStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id);
        }
        return $status;
    }

    public function getAnnounceDeliveredStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $cp_user_action_status = $this->getCpUserActionStatus($cp_user_id, $cp_action_id);
        if($cp_user_action_status->status) {
            $status = CpUserActionStatus::STATUS_ANNOUNCE_DELIVERED;
        } else {
            $status = CpUserActionStatus::STATUS_UNSENT;
        }
        return $status;
    }

    public function getQueryItem(CpAction $cp_action) {
        if ($cp_action->isLegalOpeningCpAction()) {
            $query_item = array(
                CpUserActionStatus::STATUS_FINISH,
                CpUserActionStatus::STATUS_READ,
                CpUserActionStatus::STATUS_UNREAD,
                CpUserActionStatus::STATUS_UNSENT_STR,
                CpUserActionStatus::STATUS_REJECTED
            );
        } elseif($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
            $query_item = array(
                CpUserActionStatus::STATUS_WIN,
                CpUserActionStatus::STATUS_LOSE,
                CpUserActionStatus::STATUS_UNREAD,
                CpUserActionStatus::STATUS_UNSENT_STR,
                CpUserActionStatus::STATUS_COUNT_INSTANT_WIN
            );
        } elseif($cp_action->type == CpAction::TYPE_COUPON) {
            $query_item = array(
                CpUserActionStatus::STATUS_WIN,
                null,
                CpUserActionStatus::STATUS_UNREAD,
                CpUserActionStatus::STATUS_UNSENT_STR
            );
        } elseif($cp_action->type == CpAction::TYPE_ANNOUNCE_DELIVERY) {
            $query_item = array(
                CpUserActionStatus::STATUS_ANNOUNCE_DELIVERED
            );
        } elseif ($cp_action->type == CpAction::TYPE_ANNOUNCE){
            $query_item = array(
                CpUserActionStatus::STATUS_FINISH,
                CpUserActionStatus::STATUS_READ,
                CpUserActionStatus::STATUS_UNREAD,
                CpUserActionStatus::STATUS_UNSENT_STR,
                CpUserActionStatus::STATUS_WIN,
                CpUserActionStatus::STATUS_NOT_WIN
            );
        } else {
            $query_item = array(CpUserActionStatus::STATUS_FINISH,
                CpUserActionStatus::STATUS_READ,
                CpUserActionStatus::STATUS_UNREAD,
                CpUserActionStatus::STATUS_UNSENT_STR
            );
        }

        return $query_item;
    }

    public function recoveryCpUserActionMessageAndStatus($user_id, $brand_id) {
        if (Util::isNullOrEmpty($user_id) || Util::isNullOrEmpty($brand_id)) {
            return 0;
        }
        AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

        $tables = array("cp_user_action_statuses", "cp_user_action_messages");
        $db = aafwDataBuilder::newBuilder(); // 必ずマスタにつなぐこと!

        $cp_user_ids = $db->getBySQL("SELECT cp_users.id FROM cp_users, cps WHERE cp_users.user_id = $user_id AND cp_users.cp_id = cps.id AND cps.brand_id = $brand_id", array());
        if (count($cp_user_ids) === 0) {
            return 0;
        }
        $count = 0;
        foreach ($tables as $table) {
                foreach ($cp_user_ids as $row) {
                    try {
                        $cp_user_id = $row['id'];
                        $sql = "UPDATE $table SET del_flg = 0 WHERE cp_user_id = $cp_user_id";
                        $args = array();
                        $db->executeUpdate($sql, $args);
                        $count ++;
                    } catch (Exception $e) {
                        $logger = aafwLog4phpLogger::getHipChatLogger();
                        $logger->error($e);
                        $logger->error($e->getMessage());
                        $logger->error($sql);
                    }
                }
        }
        return $count;
    }

    public function deleteCpUserActionStatusByCpActionId($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $cp_user_status = $this->cp_user_action_statuses->find(array('cp_action_id' => $cp_action_id));
        if (!$cp_user_status) {
            return;
        }

        foreach ($cp_user_status as $status) {
            $this->cp_user_action_statuses->deletePhysical($status);
        }

        $this->deleteCpUserStatusCountCache($cp_action_id);
    }

    public function deleteCpUserActionStatusByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $cp_user_status = $this->cp_user_action_statuses->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$cp_user_status) {
            return;
        }

        foreach ($cp_user_status as $status) {
            $this->cp_user_action_statuses->deletePhysical($status);
        }

        $this->deleteCpUserStatusCountCache($cp_action_id);
    }

    public function deleteCpUserActionMessagesByCpActionId($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $cp_user_messages = $this->cp_user_action_messages->find(array('cp_action_id' => $cp_action_id));
        if (!$cp_user_messages) {
            return;
        }

        foreach ($cp_user_messages as $message) {
            $this->cp_user_action_messages->deletePhysical($message);
        }

        $this->deleteCpUserMessageCountCache($cp_action_id);
    }

    public function deleteCpUserActionMessagesByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $cp_user_messages = $this->cp_user_action_messages->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$cp_user_messages) {
            return;
        }

        foreach ($cp_user_messages as $message) {
            $this->cp_user_action_messages->deletePhysical($message);
        }

        $this->deleteCpUserMessageCountCache($cp_action_id);
    }

    public function deleteCpUserMessageCountCache($cp_action_id) {
        //カウントキャッシュ削除
        $this->getCacheManager()->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_SEND_MESSAGE, $cp_action_id));
        $this->getCacheManager()->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE, $cp_action_id));
        $this->getCacheManager()->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_action_id));
    }

    public function deleteCpUserStatusCountCache($cp_action_id) {
        //カウントキャッシュ削除
        $this->getCacheManager()->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_action_id));
        $this->getCacheManager()->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_action_id));
    }

    public function getCpActionStatusMapByIds($cp_user_id, $cp_action_ids) {
        if ($cp_user_id === null || $cp_user_id  === '' || $cp_action_ids === null || count($cp_action_ids) === 0) {
            return array();
        }
        $filter = array('where' => 'del_flg = 0 AND cp_user_id = ' . $cp_user_id . ' AND cp_action_id IN(' . join(',', $cp_action_ids). ')');
        $result = $this->cp_user_action_statuses->find($filter);
        $map = array();
        foreach ($result as $row) {
            $map[$row->cp_action_id] = $row;
        }
        return $map;
    }
}
