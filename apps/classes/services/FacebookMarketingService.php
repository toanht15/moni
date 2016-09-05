<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class FacebookMarketingService extends aafwServiceBase {

    /** @var aafwEntityStoreBase $users */
    private $users;
    /** @var aafwEntityStoreBase $accounts */
    private $accounts;
    /** @var aafwEntityStoreBase $audiences */
    private $audiences;
    /** @var aafwEntityStoreBase $target */
    public $target;
    /** @var aafwEntityStoreBase $search_history */
    public $search_history;
    /** @var aafwEntityStoreBase $target_log_store */
    public $target_log_store;

    const LimitTargetPerRequest = 5000;

    public function __construct() {
        $this->users = $this->getModel("FacebookMarketingUsers");
        $this->accounts = $this->getModel("FacebookMarketingAccounts");
        $this->audiences = $this->getModel("FacebookMarketingAudiences");
        $this->target = $this->getModel("FacebookMarketingTargets");
        $this->search_history = $this->getModel("FacebookMarketingSearchFanHistories");
        $this->target_log_store = $this->getModel("FacebookMarketingTargetLogs");
    }

    /**
     * @param array $user_info
     * @return null
     * @throws aafwException
     */
    public function createOrUpdateUser($user_info = array()) {
        if (!$user_info["id"] || !$user_info["brand_user_relation_id"]) {
            return null;
        }
        $user = $this->getUserByMediaIdAndBrandUserRelationId($user_info["id"], $user_info["brand_user_relation_id"]);
        if (!$user) {
            $user = $this->users->createEmptyObject();
        }
        $user->brand_user_relation_id = $user_info["brand_user_relation_id"];
        $user->name = $user_info["name"];
        $user->media_id = $user_info["id"];
        $user->access_token = $user_info["access_token"];

        return $this->users->save($user);
    }

    /**
     * @param $media_id
     * @param $brand_user_relation_id
     * @return entity
     */
    public function getUserByMediaIdAndBrandUserRelationId($media_id, $brand_user_relation_id) {
        if (!$media_id || !$brand_user_relation_id) {
            return null;
        }

        return $this->users->findOne(array("media_id" => $media_id, "brand_user_relation_id" => $brand_user_relation_id));
    }

    /**
     * @param $account_info
     * @throws aafwException
     */
    public function createOrUpdateAccount($account_info) {
        if (!$account_info["marketing_user_id"] || !$account_info["account_id"]) {
            return;
        }
        $account = $this->getAccountByMarketingUserIdAndAccountId($account_info["marketing_user_id"], $account_info["account_id"]);
        if (!$account) {
            $account = $this->accounts->createEmptyObject();
        }
        $account->marketing_user_id = $account_info["marketing_user_id"];
        $account->account_id = $account_info["account_id"];
        $account->role = $account_info["role"];
        $account->status = $account_info["status"];
        $account->account_name = $account_info["account_name"];
        $account->custom_audience_tos = $account_info["custom_audience_tos"] ? 1 : 0;
        $account->web_custom_audience_tos = $account_info["web_custom_audience_tos"] ? 1 : 0;
        $account->type = $account_info["type"];

        return $this->accounts->save($account);
    }

    /**
     * @param $marketing_user_id
     * @param $account_id
     * @return entity
     */
    public function getAccountByMarketingUserIdAndAccountId($marketing_user_id, $account_id) {
        if (!$marketing_user_id || !$account_id) {
            return null;
        }

        return $this->accounts->findOne(array("marketing_user_id" => $marketing_user_id, "account_id" => $account_id));
    }

    /**
     * @param $id_num
     * @return aafwEntityContainer|array|void
     */
    public function getAccountByAccountIdNum($id_num) {
        if (!$id_num) {
            return null;
        }
        $id = "act_".$id_num;

        return $this->accounts->findOne(array("account_id" => $id));
    }

    /**
     * @param $id
     * @return entity|null
     */
    public function getAccountById($id) {
        if (!$id) {
            return null;
        }

        return $this->accounts->findOne(array("id" => $id));
    }

    /**
     * @param $id
     * @return entity|null
     */
    public function getMarketingUserById($id) {
        if (!$id) {
            return null;
        }

        return $this->users->findOne(array("id" => $id));
    }

    /**
     * @param $brand_user_relation_id
     * @param array $exclude_user_ids
     * @return aafwEntityContainer|array
     */
    public function getMarketingAccountsByBrandUserRelationId($brand_user_relation_id, $exclude_marketing_user_ids = array()) {

        if (!$brand_user_relation_id) {
            return array();
        }

        $marketing_users = $this->getMarketingUsersByBrandUserRelationId($brand_user_relation_id);
        $accounts = array();
        foreach ($marketing_users as $marketing_user) {

            if(in_array($marketing_user->id, $exclude_marketing_user_ids)) {
                continue;
            }

            $user_accounts = $this->getMarketingAccountsByMarketingUserId($marketing_user->id);
            if ($user_accounts) {
                $accounts = array_merge($accounts, $user_accounts->toArray());
            }
        }

        return $accounts;
    }

    public function getMarketingUsersByBrandUserRelationId($brand_user_relation_id) {
        if (!$brand_user_relation_id) {
            return null;
        }

        return $this->users->find(array("brand_user_relation_id" => $brand_user_relation_id));
    }

    /**
     * @param $user_id
     * @return aafwEntityContainer|array
     */
    public function getMarketingAccountsByMarketingUserId($user_id) {
        if (!$user_id) {
            return null;
        }

        return $this->accounts->find(array("marketing_user_id" => $user_id));
    }

    /**
     * @param $data
     * @return null
     * @throws Exception
     * @throws aafwException
     */
    public function createOrUpdateAudience($data) {
        if (!$data["account_id"] || !$data["id"]) {
            return null;
        }
        $account = $this->getAccountById($data["account_id"]);
        if (!$account) {
            throw new Exception ("FacebookMarketingService #createOrUpdateAudience account id ".$data["account_id"]." not found");
        }
        $audience = $this->audiences->findOne(array("account_id" => $account->id, "audience_id" => $data["id"]));
        if (!$audience) {
            $audience = $this->audiences->createEmptyObject();
        }
        $audience->account_id = $account->id;
        $audience->audience_id = $data["id"];
        $audience->name = $data["name"];
        $audience->description = $data["description"];
        $audience->operation_status = $data["operation_status"]["code"];
        $audience->availability = $data["delivery_status"]["description"];
        $audience->status = $data["status"];
        $audience->store = json_encode($data);

        return $this->audiences->save($audience);
    }

    /**
     * @param $account_id
     * @return aafwEntityContainer|array
     */
    public function getAudiencesByAccountId($account_id) {
        if (!$account_id) {
            return null;
        }

        return $this->audiences->find(array("account_id" => $account_id));
    }

    /**
     * @param $condition
     * @return aafwEntityContainer|array|null
     */
    public function getAudiencesByCondition($condition) {
        if (!$condition) {
            return null;
        }
        return $this->audiences->find($condition);
    }

    /**
     * @param $account_id
     * @return 件数
     */
    public function getAudiencesCountByAccountId($account_id) {
        if (!$account_id) {
            return 0;
        }

        return $this->audiences->count(array("account_id" => $account_id));
    }

    /**
     * @param $id
     * @return entity|null
     */
    public function getAudienceById($id) {
        if (!$id) {
            return null;
        }

        return $this->audiences->findOne(array("id" => $id));
    }

    /**
     * @param $audience_id
     * @return 件数
     */
    public function countTargetByAudienceId($audience_id) {
        if (!$audience_id) {
            return 0;
        }

        return $this->target->count(array("audience_id" => $audience_id));
    }

    /**
     * @param $audience_id
     * @param $conditions
     * @return null
     * @throws aafwException
     */
    public function createOrUpdateSearchHistory($audience_id, $conditions, $segment_condition_type = false) {
        if (!$audience_id) {
            return null;
        }
        $history = $this->search_history->findOne(array("audience_id" => $audience_id));
        if (!$history) {
            $history = $this->search_history->createEmptyObject();
            $history->audience_id = $audience_id;
        }

        $history->search_condition = $conditions;

        $history->search_type = $segment_condition_type ? FacebookMarketingSearchFanHistory::SEACH_TYPE_SEGMENT : FacebookMarketingSearchFanHistory::SEACH_TYPE_ADS;

        return $this->search_history->save($history);
    }

    /**
     * @param $history
     */
    public function updateSearchHistory($history) {
        $this->search_history->save($history);
    }

    /**
     * @param $audience_id
     * @return mixed
     */
    public function getSearchConditionByAudienceId($audience_id) {
        if (!$audience_id) {
            return array();
        }
        $history = $this->search_history->findOne(array("audience_id" => $audience_id));

        return json_decode($history->search_condition, true);
    }

    /**
     * @param $audience_id
     * @return entity
     */
    public function getSearchHistoryByAudienceId($audience_id) {
        if (!$audience_id) {
            return null;
        }

        return $this->search_history->findOne(array("audience_id" => $audience_id));
    }

    /**
     * @param $from_audience_id
     * @param $to_audience_id
     * @return null|void
     * @throws aafwException
     */
    public function copySearchHistory($from_audience_id, $to_audience_id) {
        if (!$from_audience_id || !$to_audience_id) {
            return null;
        }
        $search_history = $this->getSearchHistoryByAudienceId($from_audience_id);
        if (!$search_history) {
            return null;
        }
        
        $new_search_history = $this->search_history->createEmptyObject();
        $new_search_history->audience_id = $to_audience_id;
        $new_search_history->search_condition = $search_history->search_condition;
        $new_search_history->search_type = $search_history->search_type;

        return $this->search_history->save($new_search_history);
    }

    /**
     * @param $audience_id
     * @param bool $fetch_flg
     * @return array
     */
    public function getTargetByAudienceId($audience_id, $fetch_flg = true) {
        $db = aafwDataBuilder::newBuilder();
        $sql = "SELECT * FROM facebook_marketing_targets WHERE audience_id = " . $audience_id . " AND del_flg = 0";
        return $db->getBySQL($sql, array(array('__NOFETCH__' => $fetch_flg)));
    }

    /**
     * @param $result array ( 'resource' => りそーす, 'class' => クラス名 )
     * @param $access_token
     * @param $audience
     */
    public function addTargets($result, $access_token, $audience) {
        $db = aafwDataBuilder::newBuilder();
        $client = new FacebookMarketingApiClient($access_token);
        $i = 0;
        $fb_ids = array();
        $emails = array();
        $insert_sql = 'INSERT INTO facebook_marketing_targets (audience_id, user_id, fb_uid, email, created_at, updated_at) VALUE ';
        $value_list = '';
        while ($target = $db->fetch($result)) {
            $value = ' (' . $audience->id . ',' . $target['user_id'] . ',"' . $target['fb_uid'] . '","' . $target['email'] . '","'
                    . date('Y-m-d H:i:s') . '","' . date('Y-m-d H:i:s') . '"),';

            $value_list .= $value;

            if ($target['fb_uid']) {
                $fb_ids[] = $target['fb_uid'];
            } else if ($target['email']) {
                $emails[] = $target['email'];
            }

            $i++;
            if ($i % FacebookMarketingService::LimitTargetPerRequest == 0) {
                $client->apiAddTarget($audience->audience_id, $fb_ids, $emails);
                $fb_ids = array();
                $emails = array();
                $value_list = trim($value_list, ",");
                $sql = $insert_sql . $value_list;
                $db->executeUpdate($sql);
                $value_list = '';
            }
        }

        if ($i % FacebookMarketingService::LimitTargetPerRequest != 0) {
            $client->apiAddTarget($audience->audience_id, $fb_ids, $emails);
            $value_list = trim($value_list, ",");
            $sql = $insert_sql . $value_list;
            $db->executeUpdate($sql);
        }
    }

    /**
     * @param $result array ( 'resource' => りそーす, 'class' => クラス名 )
     * @param $access_token
     * @param $audience
     */
    public function removeTargets($result, $access_token, $audience) {
        $db = aafwDataBuilder::newBuilder();
        $client = new FacebookMarketingApiClient($access_token);
        $i = 0;
        $fb_ids = array();
        $emails = array();
        $insert_sql = 'DELETE FROM facebook_marketing_targets WHERE id IN (';
        $ids = '';
        while ($target = $db->fetch($result)) {
            $ids .= $target['id'] . ',';

            if ($target['fb_uid']) {
                $fb_ids[] = $target['fb_uid'];
            } else if ($target['email']) {
                $emails[] = $target['email'];
            }

            $i++;
            if ($i % FacebookMarketingService::LimitTargetPerRequest == 0) {
                $client->apiRemoveTarget($audience->audience_id, $fb_ids, $emails);
                $fb_ids = array();
                $emails = array();
                $ids = trim($ids, ",");
                $sql = $insert_sql . $ids . ')';
                $db->executeUpdate($sql);
                $ids = '';
            }
        }

        if ($i % FacebookMarketingService::LimitTargetPerRequest != 0) {
            $client->apiRemoveTarget($audience->audience_id, $fb_ids, $emails);
            $ids = trim($ids, ",");
            $sql = $insert_sql . $ids . ')';
            $db->executeUpdate($sql);
        }
    }
    
    /**
     * Update Fb Marketing Account And Return Update Error IDs
     * @param $marketing_users
     * @return array
     */
    public function updateFacebookMarketingAccountInfo($marketing_users) {

        $exclude_user_ids = array();

        foreach ($marketing_users as $marketing_user) {
            try {
                $marketing_accounts = $this->getMarketingAccountsByMarketingUserId($marketing_user->id);

                $account_ids = array();
                foreach ($marketing_accounts as $marketing_account) {
                    $account_ids[] = $marketing_account->account_id;
                }

                $client = new FacebookMarketingApiClient($marketing_user->access_token);
                $accounts_data = $client->getMarketingAccountsInfo($account_ids, $marketing_user);

                foreach ($accounts_data as $account_data) {
                    if ($account_data["error"]) {
                        $exclude_user_ids[] = $marketing_user->id;
                        break;
                    }
                    $account_data['type'] = FacebookMarketingAccount::FACEBOOK_ADS;
                    $this->createOrUpdateAccount($account_data);
                }
            } catch (Exception $e) {
                $exclude_user_ids[] = $marketing_user->id;
                aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
                aafwLog4phpLogger::getDefaultLogger()->error($e);
            }
        }
        
        return array_unique($exclude_user_ids);
    }

    /**
     *
     * @param $audience_id
     * @return 件数
     */
    public function calculateSendTargetCount($audience_id) {

        $filter = array(
            'audience_id' => $audience_id
        );

        return $this->target_log_store->count($filter);
    }

    /**
     * @param $audience_id
     * @return entity
     */
    public function findLastSendTargetLog($audience_id) {

        $filter = array(
            'conditions' => array(
                'audience_id' => $audience_id
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc',
            ),
        );

        return $this->target_log_store->findOne($filter);
    }

    /**
     * @param $audience_id
     * @return aafwEntityContainer|array
     */
    public function findSendTargetLogByAudienceId($audience_id) {

        $filter = array(
            'conditions' => array(
                'audience_id' => $audience_id
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc',
            ),
        );

        return $this->target_log_store->find($filter);
    }

    /**
     * @param $audience_id
     * @param $target_count
     * @throws aafwException
     */
    public function createTargetLog($audience_id, $target_count) {

        $target_log = $this->target_log_store->createEmptyObject();

        $target_log->audience_id = $audience_id;
        $target_log->total = $target_count;

        $this->target_log_store->save($target_log);
    }

    /**
     * @param $access_token
     * @param $audience
     * @throws Exception
     */
    public function deleteOldTarget($access_token, $audience) {
        try {
            $result = $this->getTargetByAudienceId($audience->id);
            $this->removeTargets($result, $access_token, $audience);
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            throw $e;
        }
    }
}
