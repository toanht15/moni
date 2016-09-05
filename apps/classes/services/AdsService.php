<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class AdsService extends aafwServiceBase {

    const LIMIT_TARGET_PER_REQUEST = 5000;

    private $ads_account_store;
    private $ads_audience_store;
    private $ads_audiences_accounts_relation_store;
    private $ads_target_log_store;
    private $ads_target_user_store;
    private $ads_user_store;

    public function __construct() {
        $this->loadModel();
    }

    /**
     * @param $ads_user_data
     * @return null
     */
    public function createOrUpdateAdsUser($ads_user_data) {

        if (!$ads_user_data['social_account_id'] || !$ads_user_data['brand_user_relation_id']) {
            return null;
        }

        $ads_user = $this->findAdsUsersByBrandUserRelationIdAndSnsAccountId($ads_user_data['brand_user_relation_id'], $ads_user_data['social_account_id']);

        if (!$ads_user) {
            $ads_user = $this->ads_user_store->createEmptyObject();
        }

        $ads_user->brand_user_relation_id = $ads_user_data['brand_user_relation_id'];
        $ads_user->name = $ads_user_data['name'];
        $ads_user->social_app_id = $ads_user_data['social_app_id'];
        $ads_user->social_account_id = $ads_user_data['social_account_id'];
        $ads_user->access_token = $ads_user_data['access_token'];
        $ads_user->secret_access_token = $ads_user_data['secret_access_token'];

        return $this->ads_user_store->save($ads_user);
    }

    /**
     * @param $ads_user_id
     * @return null
     */
    public function findAdsUserById($ads_user_id) {
        if (!$ads_user_id) {
            return null;
        }

        return $this->ads_user_store->findOne(array("id" => $ads_user_id));
    }

    /**
     * @param $brand_user_relation_id
     * @return null
     */
    public function findAdsUsersByBrandUserRelationId($brand_user_relation_id) {
        if (!$brand_user_relation_id) {
            return null;
        }

        return $this->ads_user_store->find(array("brand_user_relation_id" => $brand_user_relation_id));
    }

    /**
     * @param $brand_user_relation_id
     * @param $sns_account_id
     * @return null
     */
    public function findAdsUsersByBrandUserRelationIdAndSnsAccountId($brand_user_relation_id, $sns_account_id) {
        if (!$brand_user_relation_id || !$sns_account_id) {
            return null;
        }

        $filter = array(
            'brand_user_relation_id' => $brand_user_relation_id,
            'social_account_id' => $sns_account_id,
        );

        return $this->ads_user_store->findOne($filter);
    }




    /**
     * @param $account_info
     * @throws aafwException
     */
    public function createOrUpdateAdsAccount($account_info) {

        if (!$account_info["ads_user_id"] || !$account_info["account_id"]) {
            return;
        }
        
        $ads_account = $this->findAdsAccountsByAdsUserIdAndSnsAccountId($account_info["ads_user_id"], $account_info["account_id"],$account_info["social_app_id"]);

        if (!$ads_account) {
            $ads_account = $this->ads_account_store->createEmptyObject();
        }

        $ads_account->ads_user_id = $account_info["ads_user_id"];
        $ads_account->account_id = $account_info["account_id"];
        $ads_account->account_name = $account_info["account_name"];
        $ads_account->social_app_id = $account_info["social_app_id"];
        $ads_account->extra_data = $account_info["extra_data"];

        return $this->ads_account_store->save($ads_account);
    }

    /**
     * @param $ads_account_id
     * @return null
     */
    public function findAdsAccountById($ads_account_id) {

        if(!$ads_account_id) {
            return null;
        }

        $filter = array(
            'id' => $ads_account_id
        );

        return $this->ads_account_store->findOne($filter);
    }

    /**
     * @param $ads_account_ids
     * @return mixed
     */
    public function findAdsAccountsByIds($ads_account_ids) {

        $ads_accounts = array();

        foreach($ads_account_ids as $ads_account_id) {
            $account = $this->findAdsAccountById($ads_account_id);

            if($account) {
                $ads_accounts[] = $account;
            }
        }

        return $ads_accounts;
    }

    /**
     * @param $ads_user_id
     * @return null
     */
    public function findAdsAccountsByAdsUserId($ads_user_id) {

        if(!$ads_user_id) {
            return null;
        }

        $filter = array(
            'ads_user_id' => $ads_user_id
        );

        return $this->ads_account_store->find($filter);
    }

    /**
     * @param $ads_user_id
     * @param $sns_account_id
     * @return null
     */
    public function findAdsAccountsByAdsUserIdAndSnsAccountId($ads_user_id, $sns_account_id, $social_app_id) {

        if(!$ads_user_id || !$sns_account_id || !$social_app_id) {
            return null;
        }

        $filter = array(
            'ads_user_id' => $ads_user_id,
            'account_id' => $sns_account_id,
            'social_app_id' => $social_app_id,
        );

        return $this->ads_account_store->findOne($filter);
    }

    /**
     * Update Ads Account
     * @param $marketing_users
     * @return array
     */
    public function updateAdsAccountInfo($ads_users) {
        foreach ($ads_users as $ads_user) {

            if($ads_user->isFacebookAds()) {
                $this->updateFacebookAdsAccountInfo($ads_user);
            } elseif($ads_user->isTwitterAds()) {
                $this->updateTwitterAdsAccountInfo($ads_user);
            }
        }
    }

    public function updateFacebookAdsAccountInfo($ads_user) {

        try {

            $ads_accounts = $this->findAdsAccountsByAdsUserId($ads_user->id);

            $fb_account_ids = array();
            foreach ($ads_accounts as $ads_account) {
                $fb_account_ids[] = $ads_account->account_id;
            }

            $client = new FacebookMarketingApiClient($ads_user->access_token);
            $accounts_data = $client->fetchMarketingAccountsInfo($fb_account_ids, $ads_user);

            foreach ($accounts_data as $account_data) {
                if ($account_data["error"]) {
                    continue;
                }

                $this->createOrUpdateAdsAccount($account_data);
            }

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }

    public function updateTwitterAdsAccountInfo($ads_user) {
        try {

            $ads_accounts = $this->findAdsAccountsByAdsUserId($ads_user->id);

            $tw_account_ids = array();
            foreach ($ads_accounts as $ads_account) {
                $tw_account_ids[] = $ads_account->account_id;
            }

            $client = new TwitterAdsApiClient($ads_user->access_token, $ads_user->secret_access_token);
            $accounts_data = $client->fetchAdsAccountsInfo($tw_account_ids, $ads_user);

            foreach ($accounts_data as $account_data) {
                $this->createOrUpdateAdsAccount($account_data);
            }

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }

    /**
     *
     * @param $ads_account
     * @return array
     */
    public function convertAdsAccountInfo($ads_account) {

        $account_info = array();

        $account_info['id'] = $ads_account->id;
        $account_info['ads_user_id'] = $ads_account->ads_user_id;
        $account_info['account_id'] = $ads_account->account_id;
        $account_info['account_name'] = $ads_account->account_name;
        $account_info['social_app_id'] = $ads_account->social_app_id;

        $extra_data = json_decode($ads_account->extra_data, true);

        $account_info = array_merge($account_info, $extra_data);

        return $account_info;
    }

    /**
     * @param $brand_user_relation_id
     * @return array|null
     */
    public function findValidAdsAccountByBrandUserRelationId($brand_user_relation_id) {

        if(!$brand_user_relation_id) {
            return null;
        }

        $ads_users = $this->findAdsUsersByBrandUserRelationId($brand_user_relation_id);

        $valid_accounts = array();

        foreach($ads_users as $ads_user) {
            $accounts = $this->findValidAdsAccountByAdsUserId($ads_user->id);
            $valid_accounts = array_merge($valid_accounts, $accounts);
        }

        return $valid_accounts;
    }

    /**
     * @param $ads_user_id
     * @return array|null
     */
    public function findValidAdsAccountByAdsUserId($ads_user_id) {

        if(!$ads_user_id) {
            return null;
        }

        $ads_accounts = $this->findAdsAccountsByAdsUserId($ads_user_id);

        $valid_accounts = array();

        foreach($ads_accounts as $ads_account) {
            if($ads_account->isValidAccount()) {
                $valid_accounts[] = $ads_account;
            }
        }

        return $valid_accounts;
    }



    /**
     * @param $audience_data
     * @return mixed
     */
    public function createOrUpdateAdsAudience($audience_data) {

        if($audience_data['id']) {
            $audience = $this->findAdsAudiencesById($audience_data['id']);
        }

        if(!$audience) {
            $audience = $this->ads_audience_store->createEmptyObject();
        }

        $audience->brand_user_relation_id = $audience_data['brand_user_relation_id'];
        $audience->name = $audience_data['name'];
        $audience->description = $audience_data['description'];
        $audience->search_condition = $audience_data['search_condition'];
        $audience->search_type = $audience_data['search_type'];
        $audience->status = $audience_data['status'];

        return $this->ads_audience_store->save($audience);
    }

    /**
     * @param $ads_audience_id
     * @return null
     */
    public function findAdsAudiencesById($ads_audience_id) {

        if (!$ads_audience_id) {
            return null;
        }

        return $this->ads_audience_store->findOne(array("id" => $ads_audience_id));
    }

    /**
     * @param $brand_user_relation_id
     * @return null
     */
    public function findAdsAudiencesByBrandUserRelationId($brand_user_relation_id) {

        if (!$brand_user_relation_id) {
            return null;
        }

        return $this->ads_audience_store->find(array("brand_user_relation_id" => $brand_user_relation_id));
    }

    /**
     * Use Api And Create Sns Audience
     * @param $ads_audience
     * @param $ads_account
     * @return null|void
     */
    public function createSnsAudience($ads_audience, $ads_account) {

        if($ads_account->isFacebookAccount()) {
            return $this->createFacebookAudience($ads_audience, $ads_account);
        } elseif($ads_account->isTwitterAccount()) {
            return $this->createTwitterAudience($ads_audience, $ads_account);
        }

        return null;
    }

    /**
     * @param $ads_audience
     * @param $ads_account
     * @return array|null
     */
    public function createFacebookAudience($ads_audience, $ads_account) {

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);

        if(!$ads_user) {
            return null;
        }

        $client = new FacebookMarketingApiClient($ads_user->access_token);

        $audience_data = array(
            'name' => $ads_audience->name,
            'description' => $ads_audience->description,
        );

        return array($client->createOrUpdateCustomAudience($ads_account->account_id, null, $audience_data));
    }

    /**
     * @param $ads_audience
     * @param $ads_account
     * @return array|null
     */
    public function createTwitterAudience($ads_audience, $ads_account) {

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);

        if(!$ads_user) {
            return null;
        }

        $client = new TwitterAdsApiClient($ads_user->access_token, $ads_user->secret_access_token);

        $mail_audience = $client->createEmptyTailoredAudiences($ads_account->account_id, $ads_audience->name . '(Type: Email)', TwitterAdsApiClient::TYPE_EMAIL);
        $user_id_audience = $client->createEmptyTailoredAudiences($ads_account->account_id, $ads_audience->name . '(Type: Twitter_Id)', TwitterAdsApiClient::TYPE_TWITTER_ID);

        return array (AdsAudiencesAccountsRelation::SEND_ID_TYPE => $user_id_audience, AdsAudiencesAccountsRelation::SEND_MAIL_TYPE =>$mail_audience);
    }



    /**
     * @param $ads_audience
     * @param $ads_account
     * @return null
     */
    public function createAudiencesAccountsRelation($ads_audience, $ads_account) {

        $sns_audiences = $this->createSnsAudience($ads_audience, $ads_account);

        if(!$sns_audiences) {
            return null;
        }

        $relations = array();

        foreach($sns_audiences as $key => $sns_audience) {

            $audience_account_relation = $this->ads_audiences_accounts_relation_store->createEmptyObject();

            $audience_account_relation->ads_audience_id = $ads_audience->id;
            $audience_account_relation->ads_account_id = $ads_account->id;
            $audience_account_relation->extra_data = json_encode($sns_audience);

            if($ads_account->isFacebookAccount()) {
                $audience_account_relation->type = AdsAudiencesAccountsRelation::SEND_MIXED_TYPE;
                $audience_account_relation->sns_audience_id = $sns_audience['id'];

                $relations[] = $this->ads_audiences_accounts_relation_store->save($audience_account_relation);
            } elseif($ads_account->isTwitterAccount()) {

                $audience_account_relation->sns_audience_id = $sns_audience->id;
                $audience_account_relation->type = $key;

                $relations[] = $this->ads_audiences_accounts_relation_store->save($audience_account_relation);
            }
        }

        return $relations;
    }

    public function updateFacebookRelation($ads_audience, $ads_account) {

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);
        $client = new FacebookMarketingApiClient($ads_user->access_token);

        $audience_data = array(
            'name' => $ads_audience->name,
            'description' => $ads_audience->description,
        );

        $relation = $this->findRelationByAccountIdAndAudienceIdAndType($ads_account->id, $ads_audience->id, AdsAudiencesAccountsRelation::SEND_MIXED_TYPE);

        $sns_audience = $client->createOrUpdateCustomAudience($ads_account->account_id, $relation->sns_audience_id, $audience_data);

        $relation->extra_data = json_encode($sns_audience);

        return $this->updateAudiencesAccountsRelation($relation);
    }

    /**
     * @param $relation
     */
    public function updateAudiencesAccountsRelation($relation) {
        return $this->ads_audiences_accounts_relation_store->save($relation);
    }

    /**
     * @param $ads_account_id
     * @return null
     */
    public function findAudiencesAccountsRelationsByAccountId($ads_account_id) {

        if(!$ads_account_id) {
            return null;
        }

        $filter = array(
            'ads_account_id' => $ads_account_id
        );

        return $this->ads_audiences_accounts_relation_store->find($filter);
    }

    /**
     * @param $ads_account_id
     * @param $ads_audience_id
     * @return null
     */
    public function findAudiencesAccountsRelationsByAccountIdAndAudienceId($ads_account_id, $ads_audience_id) {

        if(!$ads_account_id || !$ads_audience_id) {
            return null;
        }

        $filter = array(
            'ads_account_id' => $ads_account_id,
            'ads_audience_id' => $ads_audience_id,
        );

        return $this->ads_audiences_accounts_relation_store->find($filter);
    }

    /**
     * @param $id
     * @return null
     */
    public function findAudiencesAccountsRelationById($id) {

        if(!$id) {
            return null;
        }

        return $this->ads_audiences_accounts_relation_store->findOne(array('id' => $id));
    }

    /**
     * @param $ads_account_id
     * @param $ads_audience_id
     * @param $type
     * @return null
     */
    public function findRelationByAccountIdAndAudienceIdAndType($ads_account_id, $ads_audience_id, $type) {

        if(!$ads_account_id || !$ads_audience_id) {
            return null;
        }

        $filter = array(
            'ads_account_id' => $ads_account_id,
            'ads_audience_id' => $ads_audience_id,
            'type' => $type,
        );

        return $this->ads_audiences_accounts_relation_store->findOne($filter);
    }

    /**
     * @return mixed
     */
    public function findAutoSendTargetRelations() {
        return $this->ads_audiences_accounts_relation_store->find(array(
            'auto_send_target_flg' => AdsAudiencesAccountsRelation::AUTO_SEND_TARGET_FLG_ON
        ));
    }



    /**
     * @param $audiences_accounts_relation_id
     * @param $total
     * @param $status
     * @return mixed
     */
    public function createTargetLog($audiences_accounts_relation_id, $total, $status) {

        $target_log = $this->ads_target_log_store->createEmptyObject();

        $target_log->ads_audiences_accounts_relation_id = $audiences_accounts_relation_id;
        $target_log->total = $total;
        $target_log->status = $status;

        return $this->ads_target_log_store->save($target_log);
    }

    /**
     * @param $ads_audiences_accounts_relation_id
     * @param null $order
     * @return null
     */
    public function findAdsTargetLogsByAudiencesAccountsRelationIds($ads_audiences_accounts_relation_ids, $order = null) {

        if(!$ads_audiences_accounts_relation_ids) {
            return null;
        }

        $filter = array(
            'conditions' => array(
                'ads_audiences_accounts_relation_id', 'IN', $this->encloseArray($ads_audiences_accounts_relation_ids)
            ),
            'order' => $order,
        );

        return $this->ads_target_log_store->find($filter);
    }

    /**
     * @param $ads_audiences_accounts_relation_ids
     * @return null
     */
    public function findLastSendTarget($ads_audiences_accounts_relation_ids) {

        if(!$ads_audiences_accounts_relation_ids) {
            return null;
        }

        $db = aafwDataBuilder::newBuilder();

        $param = array(
            'relation_ids' => $ads_audiences_accounts_relation_ids,
        );

        $pager = array(
            'page' => 1,
            'count' => 1,
        );

        $order = array(
            'name' => 'created_at',
            'direction' => 'desc',
        );

        $result = $db->getAdsTargetLogsByRelationIds($param, $order, $pager, true, 'AdsTargetLog');

        if(!$result) {
            return null;
        }

        return $result['list'][0];
    }

    /**
     * @param $relation_id
     */
    public function deleteAdsTargetUser($relation_id) {

        if(!$relation_id) {
            return;
        }

        $sql = 'DELETE FROM ads_target_users WHERE ads_audiences_accounts_relation_id = "'.$relation_id.'"';

        $db = aafwDataBuilder::newBuilder();
        $db->executeUpdate($sql);
    }



    public function sendTarget($search_condition,$page_info,$target_count, $audiences_accounts_relations) {

        $db = aafwDataBuilder::newBuilder();

        foreach($audiences_accounts_relations as $relation) {

            try {

                if(!$search_condition) {
                    $this->createTargetLog($relation->id, 0, AdsTargetLog::SEND_TARGET_SUCCESS);
                    continue;
                }

                $ads_account = $this->findAdsAccountById($relation->ads_account_id);
                $ads_audience = $this->findAdsAudiencesById($relation->ads_audience_id);

                if($ads_account->isFacebookAccount()) {

                    $sql = $this->buildFacebookQueryTargetSql($search_condition,$page_info, $ads_audience->search_type);
                    $target_result = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));
                    $count = $target_result['resource']->num_rows;
                    $this->sendFacebookTarget($target_result, $relation);

                } elseif($ads_account->isTwitterAccount()) {

                    $sql = $this->buildTwitterQueryTargetSql($search_condition,$page_info, $ads_audience->search_type);
                    $target_result = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));
                    $count = $target_result['resource']->num_rows;
                    $this->sendTwitterTarget($target_result, $relation);
                }

                $target_count = $target_count ? $target_count : $count;

                $this->createTargetLog($relation->id, $target_count, AdsTargetLog::SEND_TARGET_SUCCESS);

            } catch(Exception $e) {

                aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
                aafwLog4phpLogger::getDefaultLogger()->error($e);

                $this->createTargetLog($relation->id, $target_count, AdsTargetLog::SEND_TARGET_FAIL);
            }
        }
    }

    public function sendFacebookTarget($target_result, $audiences_accounts_relation) {

        $db = aafwDataBuilder::newBuilder();

        $ads_account = $this->findAdsAccountById($audiences_accounts_relation->ads_account_id);

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);

        $client = new FacebookMarketingApiClient($ads_user->access_token);

        $i = 0;
        $fb_ids = array();
        $emails = array();
        $insert_sql = 'INSERT INTO ads_target_users (ads_audiences_accounts_relation_id, user_id, sns_uid, email, created_at, updated_at) VALUE ';
        $value_list = '';
        while ($target = $db->fetch($target_result)) {
            $value = ' (' . $audiences_accounts_relation->id . ',' . $target['user_id'] . ',"' . $target['fb_uid'] . '","' . $target['email'] . '","'
                . date('Y-m-d H:i:s') . '","' . date('Y-m-d H:i:s') . '"),';

            $value_list .= $value;

            if ($target['fb_uid']) {
                $fb_ids[] = $target['fb_uid'];
            } else if ($target['email']) {
                $emails[] = $target['email'];
            }

            $i++;
            if ($i % self::LIMIT_TARGET_PER_REQUEST == 0) {
                $client->apiAddTarget($audiences_accounts_relation->sns_audience_id, $fb_ids, $emails);
                $fb_ids = array();
                $emails = array();
                $value_list = trim($value_list, ",");
                $sql = $insert_sql . $value_list;
                $db->executeUpdate($sql);
                $value_list = '';
            }
        }

        if ($i % self::LIMIT_TARGET_PER_REQUEST != 0) {
            $client->apiAddTarget($audiences_accounts_relation->sns_audience_id, $fb_ids, $emails);
            $value_list = trim($value_list, ",");
            $sql = $insert_sql . $value_list;
            $db->executeUpdate($sql);
        }
    }

    public function sendTwitterTarget($target_result, $audiences_accounts_relation) {

        if($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_MAIL_TYPE) {
            $audience_type = TwitterAdsApiClient::TYPE_EMAIL;
        } elseif($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_ID_TYPE) {
            $audience_type = TwitterAdsApiClient::TYPE_TWITTER_ID;
        } else {
            throw new \Exception('Invalid Audience Type');
        }

        $db = aafwDataBuilder::newBuilder();

        $ads_account = $this->findAdsAccountById($audiences_accounts_relation->ads_account_id);
        if(!$ads_account) {
            return;
        }

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);

        if(!$ads_user) {
            return;
        }

        $client = new TwitterAdsApiClient($ads_user->access_token,$ads_user->secret_access_token);

        $i = 0;
        $tw_ids = array();
        $emails = array();
        $insert_sql = 'INSERT INTO ads_target_users (ads_audiences_accounts_relation_id, user_id, sns_uid, email, created_at, updated_at) VALUE ';
        $value_list = '';

        while ($target = $db->fetch($target_result)) {

            if($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_MAIL_TYPE) {

                if(!$target['tw_uid'] && $target['email']) {
                    $value = ' (' . $audiences_accounts_relation->id . ',' . $target['user_id'] . ',"' . $target['tw_uid'] . '","' . $target['email'] . '","'
                        . date('Y-m-d H:i:s') . '","' . date('Y-m-d H:i:s') . '"),';
                    $value_list .= $value;

                    $emails[] = $target['email'];

                    $i++;
                }

            } elseif($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_ID_TYPE) {

                if($target['tw_uid']) {
                    $value = ' (' . $audiences_accounts_relation->id . ',' . $target['user_id'] . ',"' . $target['tw_uid'] . '","' . $target['email'] . '","'
                        . date('Y-m-d H:i:s') . '","' . date('Y-m-d H:i:s') . '"),';
                    $value_list .= $value;

                    $tw_ids[] = $target['tw_uid'];

                    $i++;
                }

            }

            if ($i > 0 && $i % self::LIMIT_TARGET_PER_REQUEST == 0) {

                $value_list = trim($value_list, ",");
                $sql = $insert_sql . $value_list;
                $db->executeUpdate($sql);
                $value_list = '';
            }
        }

        if ($i % self::LIMIT_TARGET_PER_REQUEST != 0) {
            $value_list = trim($value_list, ",");
            $sql = $insert_sql . $value_list;
            $db->executeUpdate($sql);
        }

        if($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_MAIL_TYPE && count($emails) > 0) {
            $client->updateTailoredAudiences($ads_account->account_id,$audiences_accounts_relation->sns_audience_id, $audience_type, $emails);
        } elseif($audiences_accounts_relation->type == AdsAudiencesAccountsRelation::SEND_ID_TYPE && count($tw_ids) > 0) {
            $client->updateTailoredAudiences($ads_account->account_id,$audiences_accounts_relation->sns_audience_id, $audience_type, $tw_ids);
        }
    }

    public function removeTarget($search_condition,$page_info, $audiences_accounts_relations) {

        $db = aafwDataBuilder::newBuilder();

        foreach($audiences_accounts_relations as $relation) {

            $ads_account = $this->findAdsAccountById($relation->ads_account_id);
            $ads_audience = $this->findAdsAudiencesById($relation->ads_audience_id);

            if($ads_account->isFacebookAccount()) {
                $sql = $this->buildFacebookQueryTargetSql($search_condition,$page_info, $ads_audience->search_type);
                $target_result = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));
                $this->removeFacebookTarget($target_result, $relation);
            }

            $this->deleteAdsTargetUser($relation->id);
        }
    }

    public function removeFacebookTarget($target_result, $audiences_accounts_relation) {

        $db = aafwDataBuilder::newBuilder();

        $ads_account = $this->findAdsAccountById($audiences_accounts_relation->ads_account_id);
        if(!$ads_account) {
            return;
        }

        $ads_user = $this->findAdsUserById($ads_account->ads_user_id);

        if(!$ads_user) {
            return;
        }

        $client = new FacebookMarketingApiClient($ads_user->access_token);

        $i = 0;
        $fb_ids = array();
        $emails = array();

        while ($target = $db->fetch($target_result)) {

            if ($target['fb_uid']) {
                $fb_ids[] = $target['fb_uid'];
            } else if ($target['email']) {
                $emails[] = $target['email'];
            }

            $i++;
            if ($i % self::LIMIT_TARGET_PER_REQUEST == 0) {
                $client->apiRemoveTarget($audiences_accounts_relation->sns_audience_id, $fb_ids, $emails);
                $fb_ids = array();
                $emails = array();
            }
        }

        if ($i % self::LIMIT_TARGET_PER_REQUEST != 0) {
            $client->apiRemoveTarget($audiences_accounts_relation->sns_audience_id, $fb_ids, $emails);
        }
    }

    private function buildFacebookQueryTargetSql($search_condition,$page_info,$condition_type) {

        if($condition_type == AdsAudience::SEACH_TYPE_ADS) {
            /** @var SegmentCreateSqlService $create_sql_service */
            $create_sql_service = $this->getService('SegmentCreateSqlService');
            $create_sql_service->resetCurrentParameter();
            return $create_sql_service->getUserSql($page_info, $search_condition, '', null, null, array(CpCreateSqlService::COLUMN_EMAIL, CpCreateSqlService::COLUMN_FBID));
        } elseif($condition_type == AdsAudience::SEACH_TYPE_SEGMENT) {
            /** @var CpCreateSqlService $create_sql_service */
            $create_sql_service = $this->getService('CpCreateSqlService');
            $create_sql_service->resetCurrentParameter();
            return $create_sql_service->getUserSql($page_info, $search_condition, '', null, null, array(CpCreateSqlService::COLUMN_EMAIL, CpCreateSqlService::COLUMN_FBID));
        }
    }

    private function buildTwitterQueryTargetSql($search_condition,$page_info,$condition_type) {

        if($condition_type == AdsAudience::SEACH_TYPE_ADS) {
            /** @var SegmentCreateSqlService $create_sql_service */
            $create_sql_service = $this->getService('SegmentCreateSqlService');
            return $create_sql_service->getUserSql($page_info, $search_condition, '', null, null, array(CpCreateSqlService::COLUMN_EMAIL, CpCreateSqlService::COLUMN_TWID));
        } elseif($condition_type == AdsAudience::SEACH_TYPE_SEGMENT) {
            /** @var CpCreateSqlService $create_sql_service */
            $create_sql_service = $this->getService('CpCreateSqlService');
            return $create_sql_service->getUserSql($page_info, $search_condition, '', null, null, array(CpCreateSqlService::COLUMN_EMAIL, CpCreateSqlService::COLUMN_TWID));
        }
    }

    private function loadModel() {
        $this->ads_account_store = $this->getModel('AdsAccounts');
        $this->ads_audience_store = $this->getModel('AdsAudiences');
        $this->ads_audiences_accounts_relation_store = $this->getModel('AdsAudiencesAccountsRelations');
        $this->ads_target_log_store = $this->getModel('AdsTargetLogs');
        $this->ads_target_user_store = $this->getModel('AdsTargetUsers');
        $this->ads_user_store = $this->getModel('AdsUsers');
    }

    private function encloseArray($source_array) {
        return '(' . implode(',', $source_array) . ')';
    }
}