<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpMessageDeliveryService');
AAFW::import('jp.aainc.classes.services.ShippingAddressUserService');
AAFW::import('jp.aainc.classes.services.UpdateFanTargetService');
AAFW::import('jp.aainc.classes.util.AddressChecker');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');

class update_fan_target extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_fan_target';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $message_delivery_service;
    protected $reservation;
    protected $targets_store;

    protected $service_factory;

    /** @var aafwDataBuilder $data_builder  */
    protected $data_builder;

    protected $logger;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '256M');
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->service_factory = new aafwServiceFactory();
        $this->targets_store = aafwEntityStoreFactory::create('CpMessageDeliveryTargets');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $cp_validator = new CpValidator($this->Data['brand']->id);
        if(!$cp_validator->isOwner($this->cp_id)) {
            $errors['cp_id'] = '操作が失敗しました。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }
        if(!$cp_validator->isOwnerOfAction($this->action_id)) {
            $errors['action_id'] = '操作が失敗しました。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        if ($this->update_type != CpMessageDeliveryService::RANDOM_TARGET && $this->update_type != CpMessageDeliveryService::FIX_TARGET && $this->update_type != CpMessageDeliveryService::CANCEL_FIX_TARGET) {
            if (!$this->user) {
                $errors['updateTargetError'] = '対象ユーザを選択してください。';
                $json_data = $this->createAjaxResponse("ng", array(), $errors);
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        /** @var  CpMessageDeliveryService $message_delivery_service */
        $this->message_delivery_service = $this->createService('CpMessageDeliveryService');
        $this->reservation = $this->message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->action_id);
        if(!$this->reservation) {
            $errors['updateTargetError'] = '送信予約が設定されていません。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction(){
        if($this->reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "show_reservation_info", array("action_id" => $this->action_id));
        }

        /** @var CpMessageDeliveryService $service */
        $service = CpMessageDeliveryService;
        if ($this->POST['select_all_users'] &&
            $this->update_type != $service::RANDOM_TARGET) {
            $searchQuery = $this->getSearchQuery(array(
                'cp_id'     => $this->POST['cp_id'],
                'brand_id'  => $this->Data['brand']->id),
                $this->getSearchConditionSession($this->POST['cp_id']));

            if ($this->update_type == $service::ADD_TARGET) {
                try {
                    $this->begin();
                    $target_info[$service::ADD_TARGET] = $this->countForInsertionAllFans($searchQuery);
                    $this->insertAllFans($searchQuery);
                    $this->commit();
                } catch(Exception $e) {
                    $this->rollback();
                    $this->responseError($e);
                    return 'dummy.php';
                }
            } else if($this->update_type == $service::DELETE_TARGET){
                try {
                    $this->begin();
                    $target_info[$service::DELETE_TARGET] = $this->countForDeletionAllFans($searchQuery);
                    $this->deleteAllFans($searchQuery);
                    $this->commit();
                } catch(Exception $e) {
                    $this->rollback();
                    $this->responseError($e);
                    return 'dummy.php';
                }
            }
        } else {
            $userIds = $this->concatUserIds();
            if ($this->update_type == $service::ADD_TARGET) {
                try {
                    $this->begin();
                    $target_info[$service::ADD_TARGET] = $this->countForInsertionTargets($userIds);
                    $this->insertTargets($userIds);
                    $this->commit();
                } catch (Exception $e) {
                    $this->rollback();
                    $this->responseError($e);
                    return 'dummy.php';
                }
            } elseif ($this->update_type == $service::RANDOM_TARGET) {
                    // 住所重複ユーザの取得
                    $notIn = '';
                    $limit = '';
                    $check_shipping_address_flg = 0;
                    $searchQuery = $this->getSearchQuery(array(
                        'cp_id'     => $this->POST['cp_id'],
                        'brand_id'  => $this->Data['brand']->id),
                        $this->getSearchConditionSession($this->POST['cp_id']));
                    if ($this->duplicated_address_check) {
                        $update_fan_target_service = $this->createService('UpdateFanTargetService');
                        $duplicatedUserIds = $update_fan_target_service->getUserIdsWithDuplicatedAddress(
                            $this->reservation->id,
                            $this->POST['cp_id'],
                            $this->POST['action_id'],
                            $searchQuery
                        );
                        // 除外ユーザ、取得件数を考慮したSQL生成
                        if (count($duplicatedUserIds) > 0) {
                            $notIn = ' NOT IN (' . implode(',', $duplicatedUserIds) . ')';
                        }
                        // 配送情報を入力しているユーザに限定する
                        $check_shipping_address_flg = 1;
                    }
                    if ($this->random_target_num > 0)  {
                        // ランダム抽出のため、Order句も付加する
                        $limit = ' ORDER BY RAND() LIMIT ' . $this->random_target_num;
                    }
                    // 登録処理
                    try {
                        $this->begin();
                        $totalCount = $this->countForInsertionAllFans($searchQuery, $notIn, $check_shipping_address_flg);
                        $target_info[$service::RANDOM_TARGET] =
                            $this->random_target_num < $totalCount ? $this->random_target_num : $totalCount;
                        $this->insertAllFans($searchQuery, $notIn, $limit, $check_shipping_address_flg);
                        $this->commit();
                    } catch(Exception $e) {
                        $this->rollback();
                        $this->responseError($e);
                        return 'dummy.php';
                    }
            } else if($this->update_type == CpMessageDeliveryService::DELETE_TARGET){
                try {
                    $this->begin();
                    $target_info[$service::DELETE_TARGET] = $this->countForDeletionTargets($userIds);
                    $this->deleteTargets($userIds);
                    $this->commit();
                } catch(Exception $e) {
                    $this->rollback();
                    $this->responseError($e);
                    return 'dummy.php';
                }
            }
        }

        //当選者確定
        if ($this->update_type == CpMessageDeliveryService::FIX_TARGET) {
            try {
                $this->begin();
                $target_info[$service::FIX_TARGET] = $this->countForFixTargets();
                $this->fixTargets();
                $this->commit();
            } catch (Exception $e) {
                $this->rollback();
                $this->responseError($e);
                return 'dummy.php';
            }
        }

        //当選者解除
        if ($this->update_type == CpMessageDeliveryService::CANCEL_FIX_TARGET) {
            try {
                $this->begin();
                $target_info[$service::CANCEL_FIX_TARGET] = $this->countForCancelFixTargets();
                $this->cancelFixTargets();
                $this->commit();
            } catch (Exception $e) {
                $this->rollback();
                $this->responseError($e);
                return 'dummy.php';
            }
        }

        $this->assign('json_data', $this->createAjaxResponse("ok", $target_info));

        return 'dummy.php';
    }

    private function countForInsertionTargets($userIds) {
        $countTargets_prepend = "SELECT COUNT(*) FROM users u WHERE u.id IN(";
        $countTargets_subsequent = ")
       AND NOT EXISTS(
         SELECT user_id FROM cp_user_action_messages m
          INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
          WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
          AND NOT EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
          WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.id AND t.del_flg = 0) AND u.del_flg = 0";

        $countTargets = $countTargets_prepend . $userIds . $countTargets_subsequent;
        $countResult = $this->data_builder->getBySQL($countTargets, array());

        return $actualCount = (int) $countResult[0]['COUNT(*)'];
    }

    private function countForDeletionTargets($userIds) {
        $countTargets_prepend = "SELECT COUNT(*) FROM users u WHERE u.id IN(";
        $countTargets_subsequent = ")
           AND NOT EXISTS(
             SELECT user_id FROM cp_user_action_messages m
              INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
              WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
           AND EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
              WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.id AND t.del_flg = 0) AND u.del_flg = 0";

        $countTargets = $countTargets_prepend . $userIds . $countTargets_subsequent;
        $countResult = $this->data_builder->getBySQL($countTargets, array());

        return $actualCount = (int) $countResult[0]['COUNT(*)'];
    }

    private function insertTargets($userIds) {
        $insertTargets_prepend =   "INSERT INTO cp_message_delivery_targets(cp_message_delivery_reservation_id, user_id, cp_action_id, status, created_at, updated_at)
            SELECT " . $this->reservation->id . ", u.id, " . $this->action_id . ", 0, NOW(), NOW() FROM users u WHERE u.id IN(";
        $insertTargets_subsequent = ")
           AND NOT EXISTS(
             SELECT user_id FROM cp_user_action_messages m
              INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
              WHERE m.cp_action_id =  " . $this->action_id . " AND c1.user_id = u.id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
           AND NOT EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
           WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.id AND t.del_flg = 0) AND u.del_flg = 0";

        $insertTargets = $insertTargets_prepend . $userIds . $insertTargets_subsequent;
        $result = $this->data_builder->executeUpdate($insertTargets);
        if (!$result) {
            throw new aafwException("INSERTION FAILED!");
        }
    }

    private function deleteTargets($userIds) {
        $deleteTargets_prepend =   "DELETE FROM cp_message_delivery_targets WHERE cp_message_delivery_reservation_id = " . $this->reservation->id . "
          AND user_id IN(SELECT u.id FROM users u WHERE u.id IN(";
        $deleteTargets_subsequent = ")
           AND NOT EXISTS(
             SELECT user_id FROM cp_user_action_messages m
              INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
              WHERE m.cp_action_id =  " . $this->action_id . " AND c1.user_id = u.id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)) AND del_flg = 0";

        $deleteTargets = $deleteTargets_prepend . $userIds . $deleteTargets_subsequent;
        $result = $this->data_builder->executeUpdate($deleteTargets);
        if (!$result) {
            throw new aafwException("DELETION FAILED!");
        }
    }

    private function countForInsertionAllFans($searchQuery, $notIn='', $check_shipping_address_flg=0) {
        $countAllFans = "SELECT COUNT(*) FROM (" . $searchQuery . ") u
            WHERE
                NOT EXISTS(
                 SELECT user_id FROM cp_user_action_messages m
                  INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
                  WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.user_id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
                AND
                NOT EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
                  WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.user_id AND t.del_flg = 0)";
        if ($notIn) {
            $countAllFans .= ' AND u.user_id' . $notIn;
        }
        if ($check_shipping_address_flg) {
            $countAllFans .= " AND EXISTS(
                SELECT s.cp_user_id FROM shipping_address_users s
                INNER JOIN cp_users c2 ON c2.id = s.cp_user_id
                WHERE c2.user_id = u.user_id AND c2.cp_id = " . $this->cp_id . " AND c2.del_flg = 0)";
        }

        $countResult = $this->data_builder->getBySQL($countAllFans, array());

        return $actualCount = (int) $countResult[0]['COUNT(*)'];
    }

    private function insertAllFans($searchQuery, $notIn='', $limit='', $check_shipping_address_flg=0) {
        $insertAllFans = "INSERT INTO cp_message_delivery_targets(cp_message_delivery_reservation_id, user_id, cp_action_id, status, created_at, updated_at)
            SELECT " . $this->reservation->id . ", u.user_id, " . $this->action_id . ", 0, NOW(), NOW()  FROM (" . $searchQuery . ") u
               WHERE
                NOT EXISTS(
                 SELECT user_id FROM cp_user_action_messages m
                  INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
                  WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.user_id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
                AND
                NOT EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
                  WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.user_id AND t.del_flg = 0)";
        if ($notIn) {
            $insertAllFans .= ' AND u.user_id ' . $notIn;
        }
        if ($check_shipping_address_flg) {
            $insertAllFans .= " AND EXISTS(
                SELECT s.cp_user_id FROM shipping_address_users s
                INNER JOIN cp_users c2 ON c2.id = s.cp_user_id 
                WHERE c2.user_id = u.user_id AND c2.cp_id = " . $this->cp_id . " AND c2.del_flg = 0)";
        }
        if ($limit) {
            $insertAllFans .= $limit;
        }

        $result = $this->data_builder->executeUpdate($insertAllFans);
        if (!$result) {
            throw new aafwException("INSERTION FAILED!");
        }
    }

    private function countForDeletionAllFans($searchQuery) {
        $countAllFans = "SELECT COUNT(*) FROM (" . $searchQuery . ") u
            WHERE
             NOT EXISTS(
              SELECT user_id FROM cp_user_action_messages m
               INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
               WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.user_id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0)
               AND
                 EXISTS(SELECT t.user_id FROM cp_message_delivery_targets t
                  WHERE t.cp_message_delivery_reservation_id = " . $this->reservation->id . " AND t.user_id = u.user_id AND t.del_flg = 0)";
        $countResult = $this->data_builder->getBySQL($countAllFans, array());

        return $actualCount = (int) $countResult[0]['COUNT(*)'];
    }

    private function deleteAllFans($searchQuery) {
        $deleteAllFans = "DELETE FROM cp_message_delivery_targets WHERE cp_message_delivery_reservation_id = " . $this->reservation->id . " AND user_id IN (SELECT u.user_id FROM (" . $searchQuery . ") u
           WHERE
            NOT EXISTS(
             SELECT c1.user_id FROM cp_user_action_messages m
              INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
              WHERE m.cp_action_id = " . $this->action_id . " AND c1.user_id = u.user_id AND c1.cp_id = " . $this->cp_id . " AND m.del_flg = 0 AND c1.del_flg = 0))";
        $result = $this->data_builder->executeUpdate($deleteAllFans);
        if (!$result) {
            throw new aafwException("DELETION FAILED!");
        }
    }

    private function countForFixTargets() {
        $countSQL = "SELECT COUNT(*) FROM cp_message_delivery_targets WHERE cp_message_delivery_reservation_id = " . $this->reservation->id. " AND fix_target_flg = 0";
        $countResult = $this->data_builder->getBySQL($countSQL, array());

        return (int)$countResult[0]['COUNT(*)'];
    }

    private function fixTargets() {
        $updateSQL = "UPDATE cp_message_delivery_targets
                        SET fix_target_flg = ".CpMessageDeliveryTarget::FIX_TARGET_ON."
                        WHERE cp_message_delivery_reservation_id = ".$this->reservation->id." AND fix_target_flg = ".CpMessageDeliveryTarget::FIX_TARGET_OFF;
        $result = $this->data_builder->executeUpdate($updateSQL);
        if (!$result) {
            throw new aafwException("FIX TARGET FAILED!");
        }

    }

    private function countForCancelFixTargets() {
        $countSQL = "SELECT COUNT(*) FROM cp_message_delivery_targets WHERE cp_message_delivery_reservation_id = " . $this->reservation->id. " AND fix_target_flg = 1";
        $countResult = $this->data_builder->getBySQL($countSQL, array());

        return (int)$countResult[0]['COUNT(*)'];
    }


    private function cancelFixTargets() {
        $updateSQL = "UPDATE cp_message_delivery_targets
                        SET fix_target_flg = ".CpMessageDeliveryTarget::FIX_TARGET_OFF."
                        WHERE cp_message_delivery_reservation_id = ".$this->reservation->id." AND fix_target_flg = ".CpMessageDeliveryTarget::FIX_TARGET_ON;
        $result = $this->data_builder->executeUpdate($updateSQL);
        if (!$result) {
            throw new aafwException("CANCEL FIX TARGET FAILED!");
        }

    }

    private function getSearchQuery($page_info, $search_condition) {
        $create_sql_service = $this->service_factory->create("CpCreateSqlService");
        return $create_sql_service->getUserSql($page_info, $search_condition, null, null, null);
    }

    private function concatUserIds() {
        $userIds = "";
        $userCount = count($this->user);
        for ($num = 0 ; $num < $userCount ; $num ++) {
            if (!$this->user[$num]) {
                continue;
            }
            $userIds .= $this->user[$num] . ",";
        }

        if ($userCount > 0) {
            $userIds = substr($userIds, 0, strlen($userIds) - 1);
        }

        return $userIds;
    }

    private function responseError($e) {
        $this->logger->error('cp_message_delivery_targets error.' . $e);
        $this->assign('json_data', $this->createAjaxResponse('ng', [], ['db' => $e->getMessage()]));
    }

    private function begin() {
        $this->targets_store->begin();
    }

    private function commit() {
        $this->targets_store->commit();
    }

    private function rollback() {
        $this->targets_store->rollback();
    }
}
