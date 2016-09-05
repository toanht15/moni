<?php
AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.CacheManager');

require_once dirname(__FILE__) . '/../../config/define.php';

class UserAnnounceDeliveryManager {

    const LOG_LIMIT_COUNT = 10000;

    private $logger;

    /** @var $service_factory aafwServiceFactory */
    private $service_factory;

    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;

    /** @var  CpMessageDeliveryService $delivery_service */
    private $delivery_service;

    /** @var  BrandsUsersRelationService $relation_service */
    private $relation_service;

    /** @var CpTransactionService $transaction_service */
    private $transaction_service;

    public function __construct() {
        ini_set('memory_limit', '1024M');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->cp_user_service = $this->service_factory->create('CpUserService');
        $this->delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $this->relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $this->transaction_service = $this->service_factory->create("CpTransactionService");
    }

    public function doProcess() {

        $reservations = $this->delivery_service->getTargetCpAnnounceDeliveryReservation();

        foreach ($reservations as $reservation) {
            // caching
            $rsv_id = $reservation->id;

            try {
                list($cp_action, $concrete_action, $cp) = $this->cp_user_service->getCpAndActionsByCpActionId($reservation->cp_action_id);

                // caching
                $cp_id = $cp->id;
                $cp_action_id = $cp_action->id;
                $concrete_action_title = $concrete_action->title;

                /** @var Brand $brand */
                $brand = $cp->getBrand();

                $context = new UserAnnounceDeliveryManager_DeliveryContext(
                    $this->service_factory,
                    $this->cp_user_service,
                    $brand
                );
                // reservationに紐づく当選対象一覧を取得する
                $targets = $context->selectTargets($rsv_id, $brand->id);
                $targets_count = $context->countTargets($rsv_id, $brand->id);

                // target数が多い場合はログに通知する
                if ($targets && $targets_count > self::LOG_LIMIT_COUNT) {
                    $time = time();
                    aafwLog4phpLogger::getHipChatLogger()->info('UserAnnounceDeliveryManager reservation_id = ' . $rsv_id . ' Start, Count : ' . $targets_count);
                }

                // target数を扱うためのユーティリティ
                $txCxt = new UserAnnounceDeliveryManager_TxContext($targets_count);
                while ($target = $context->fetch($targets)) {

                    $txCxt->goNext($target);

                    if (!$txCxt->canProcess()) {
                        continue;
                    }

                    try {
                        if (count($txCxt->target_range) > 0) {
                            $context->begin();
                            $context->insertCpUsers($txCxt->target_range, $cp_id, $rsv_id);

                            $cp_user_ids = $context->selectCpUserIds($txCxt->target_range, $cp_id);

                            // 以下の3つはcp_user_idを利用。
                            $targetCpUsers = "";
                            foreach ($cp_user_ids as $tgt) {
                                $targetCpUsers .= $tgt['id'] . ",";
                            }
                            $targetCpUsers = substr($targetCpUsers, 0, strlen($targetCpUsers) - 1);

                            $context->insertMessages($targetCpUsers, $cp_action_id, $concrete_action_title);
                            $context->insertStatuses($targetCpUsers, $cp_action_id);

                            $context->updateDelivTargets($txCxt->target_range);
                            $context->updateRedisCache($txCxt->target_range, $cp_action_id, $brand->id);

                            $context->commit();
                        }
                    } catch (Exception $ex) {
                        // ロールバックに失敗する状況でエラー情報を記録できるとは思えないのでそのまま。
                        $context->rollback();
                        $this->logger->error('message announce delivery failed. ' . $ex . " targets = " . $targets);
                        $context->recordFailures($txCxt->target_range);
                    }
                    $txCxt->clearState();
                }

                if ($targets && $targets_count > self::LOG_LIMIT_COUNT) {
                    aafwLog4phpLogger::getHipChatLogger()->info('UserAnnounceDeliveryManager reservation_id = ' . $rsv_id . ' End, Time : ' . (time() - $time));
                }

                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->service_factory->create('CpFlowService');

                // 配信終了
                $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERED;
                $this->delivery_service->updateCpMessageDeliveryReservation($reservation);

                //送信履歴を書き直す
                $cp_flow_service->setDeliveryHistoryCacheByCpActionId($cp_action->id);

            } catch (Exception $e) {
                // 配信失敗
                $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERY_FAIL;
                $this->delivery_service->updateCpMessageDeliveryReservation($reservation);
                $this->logger->error('UserAnnounceDeliveryManager Error.' . $e);
            }

            $this->logger->info('end announce delivery. reservation_id = ' . $rsv_id);
        }
    }

}

class UserAnnounceDeliveryManager_DeliveryContext {

    /** @var $service_factory aafwServiceFactory */
    private $service_factory;

    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;

    private $db;
    private $stores;
    private $cache_manager;

    private $empty_arg = array();

    public function __construct($service_factory, $cp_user_service, $brand) {
        $this->service_factory = $service_factory;
        $this->cp_user_service = $cp_user_service;

        $this->db = aafwDataBuilder::newBuilder(); // 必ずマスタにつなぐこと!

        $this->cache_manager = new CacheManager();

        $this->transaction_service = $this->service_factory->create('CpTransactionService');

        $this->stores = aafwEntityStoreFactory::create('CpMessageDeliveryReservations');
    }

    public function selectTargets($rsv_id, $brand_id) {
        $selectTargets = "/* UserAnnounceDeliveryManager_DeliveryContext->selectTargets */
                        SELECT t.id, t.cp_message_delivery_reservation_id, t.user_id, t.cp_action_id, t.status, u.name, u.profile_image_url, u.mail_address, r.optin_flg, u.monipla_user_id
                        FROM cp_message_delivery_targets t
                            INNER JOIN users u ON t.user_id = u.id INNER JOIN brands_users_relations r ON u.id = r.user_id
                            WHERE
                                t.cp_message_delivery_reservation_id = " . $rsv_id . " AND t.del_flg = 0 AND t.status = 0 AND
                                u.del_flg = 0 AND
                                r.brand_id = " . $brand_id . " AND r.withdraw_flg = 0 AND r.del_flg = 0";

        return $this->db->getBySQL($selectTargets, array('__NOFETCH__'));
    }

    public function countTargets($rsv_id, $brand_id) {
        $countTargets = "/* UserAnnounceDeliveryManager_DeliveryContext->countTargets */
                        SELECT COUNT(*)
                        FROM cp_message_delivery_targets t
                            INNER JOIN users u ON t.user_id = u.id INNER JOIN brands_users_relations r ON u.id = r.user_id
                            WHERE
                                t.cp_message_delivery_reservation_id = " . $rsv_id . " AND t.del_flg = 0 AND t.status = 0 AND
                                u.del_flg = 0 AND
                                r.brand_id = " . $brand_id . " AND r.withdraw_flg = 0 AND r.del_flg = 0";

        $value = $this->db->getBySQL($countTargets, $this->empty_arg);
        $actualCount = (int)$value[0]['COUNT(*)'];

        return $actualCount;
    }

    public function insertCpUsers($target_range, $cp_id, $resev_id) {
        $inserIntoCpUsers =
            "/* UserAnnounceDeliveryManager_DeliveryContext->insertCpUsers */ INSERT INTO cp_users(cp_id, user_id, demography_flg, updated_at, created_at)
                SELECT " . $cp_id . ", user_id, 0, NOW(), NOW() FROM cp_message_delivery_targets T
                WHERE T.cp_message_delivery_reservation_id = " . $resev_id .
            " AND T.del_flg = 0 AND NOT EXISTS(SELECT 1 FROM cp_users WHERE T.user_id = user_id AND cp_id = " . $cp_id . " AND del_flg = 0) AND T.user_id IN(";
        foreach ($target_range as $tgt) {
            $inserIntoCpUsers .= $tgt['user_id'] . ",";
        }
        $inserIntoCpUsers = substr($inserIntoCpUsers, 0, strlen($inserIntoCpUsers) - 1);
        $inserIntoCpUsers .= ")";

        $this->command($inserIntoCpUsers);
    }

    public function selectCpUserIds($target_range, $cp_id) {
        $selectCpUserIds = "/* UserAnnounceDeliveryManager_DeliveryContext->selectCpUserIds */ SELECT id, user_id FROM cp_users WHERE cp_id=" . $cp_id . " AND user_id IN(";
        foreach ($target_range as $tgt) {
            $selectCpUserIds .= $tgt["user_id"] . ",";
        }
        $selectCpUserIds = substr($selectCpUserIds, 0, strlen($selectCpUserIds) - 1);
        $selectCpUserIds .= ")";

        return $this->db->getBySQL($selectCpUserIds, $this->empty_arg);
    }

    public function insertMessages($targetCpUsers, $cp_action_id, $concrete_action_title) {
        $insertIntoUsrActMsgs =
            "/* UserAnnounceDeliveryManager_DeliveryContext->insertMessages */ INSERT INTO cp_user_action_messages(cp_user_id, cp_action_id, title, read_flg, created_at, updated_at)
                SELECT CU.id, " . $cp_action_id . ", '" . $concrete_action_title . "',1 ,NOW(), NOW() FROM cp_users CU
                WHERE CU.id IN (" . $targetCpUsers . ") AND CU.del_flg = 0
                AND NOT EXISTS ( SELECT 1 FROM cp_user_action_messages M
                                WHERE M.cp_user_id = CU.id AND M.cp_action_id = " . $cp_action_id . " AND M.del_flg = 0)";
        $this->command($insertIntoUsrActMsgs);
    }

    public function insertStatuses($targetCpUsers, $cp_action_id) {
        $insertIntoUsrActStses =
            "/* UserAnnounceDeliveryManager_DeliveryContext->insertStatuses */ INSERT INTO cp_user_action_statuses(cp_user_id, cp_action_id, status, created_at, updated_at)
                SELECT CU.id, " . $cp_action_id . ",1 ,NOW(), NOW() FROM cp_users CU
                WHERE CU.id IN (" . $targetCpUsers . ") AND CU.del_flg = 0
                AND NOT EXISTS ( SELECT 1 FROM cp_user_action_statuses S
                                WHERE S.cp_user_id = CU.id AND S.cp_action_id = " . $cp_action_id . " AND S.del_flg = 0)";
        $this->command($insertIntoUsrActStses);
    }

    public function updateDelivTargets($target_range) {
        $updateDelivTargets = "/* UserAnnounceDeliveryManager_DeliveryContext->updateDelivTargets */ UPDATE cp_message_delivery_targets SET status=1, fix_target_flg = 1, updated_at=NOW() WHERE id IN(";
        foreach ($target_range as $tgt) {
            $updateDelivTargets .= $tgt['id'] . ",";
        }
        $updateDelivTargets = substr($updateDelivTargets, 0, strlen($updateDelivTargets) - 1);
        $updateDelivTargets .= ") AND del_flg = 0";

        $this->command($updateDelivTargets);
    }

    public function updateRedisCache($target_range, $cp_action_id, $brand_id) {
        try {
            $this->cache_manager->beginBatch();

            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_action_id));

            foreach ($target_range as $tgt) {
                $this->cache_manager->resetNotificationCount($brand_id, $tgt['user_id']);
            }

            $this->cache_manager->flushBatch();
        } catch (Exception $e) {
            log_error($e);
            $this->cache_manager->resetBatch();
        }
    }

    public function recordFailures($target_range) {
        // エラーの記録は、正常系のパスではほとんど発生しないことと、安全性重視のため、
        // レコード毎に別トランザクションで実行します。
        foreach ($target_range as $tgt) {
            try {
                $this->begin();
                $updateTargets = "/* UserAnnounceDeliveryManager_DeliveryContext->recordFailures */ UPDATE cp_message_delivery_targets SET status=2 WHERE id=" . $tgt['id'];
                $this->command($updateTargets);
                $this->commit();
            } catch (Exception $e) {
                $this->rollback();
                throw $e;
            }
        }
    }

    public function begin() {
        $this->stores->begin();
    }

    public function commit() {
        $this->stores->commit();
    }

    public function rollback() {
        $this->stores->rollback();
    }

    public function fetch($rs) {
        return $this->db->fetch($rs);
    }

    public function command($command) {
        $result = $this->db->executeUpdate($command);
        if (!$result) {
            throw new Exception("Command execution failed! : " . $command);
        }
    }
}

class UserAnnounceDeliveryManager_TxContext {

    const LIMIT = 100;
    public $count = 0;
    public $total = 0;
    public $targets_count;
    public $target_range = array();

    public function __construct($targets_count) {
        $this->targets_count = $targets_count;
    }

    public function decrementTargetsCount() {
        $this->targets_count--;
    }

    public function goNext($target) {
        array_push($this->target_range, $target);
        $this->total++;
        $this->count++;
    }

    public function canProcess() {
        return $this->count == self::LIMIT || $this->total == $this->targets_count;
    }

    public function clearState() {
        $this->count = 0;
        $this->target_range = array();
    }
}
