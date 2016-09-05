<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.aafw.db.aafwSQLEditor');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class CpUserListService extends aafwServiceBase {

    const ORDER_ASC  = 1;
    const ORDER_DESC = 2;

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->cp_flow_service = $this->service_factory->create('CpFlowService');
        $this->message_delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $this->cp_user_service = $this->service_factory->create('CpUserService');
        $this->brand_service = $this->service_factory->create('BrandService');
    }

    public function getPageUserMessage($user_ids, $cp, $action, $reservation) {
        if ($user_ids === null || count($user_ids) === 0) {
            $user_ids = array("null");
        }
        $page_user_sql = "
            SELECT U.id user_id,T.id target_id,M.id message_id, T.fix_target_flg
              FROM users U
              LEFT OUTER JOIN cp_users CU ON U.id = CU.user_id AND CU.cp_id = ".$cp->id." AND CU.del_flg = 0
              LEFT OUTER JOIN cp_message_delivery_targets T ON T.user_id = U.id AND T.cp_message_delivery_reservation_id = ".$reservation->id." AND T.del_flg = 0
              LEFT OUTER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.cp_action_id = ".$action->id." AND M.del_flg = 0
             WHERE U.id IN (".implode(',',$user_ids).") AND U.del_flg = 0 ";
        $db = new aafwDataBuilder();
        $page_users = $db->getBySQL($page_user_sql, array());
        foreach($page_users as $page_user) {
            $page_user_message[$page_user['user_id']][] = $page_user['message_id'];
            $page_user_message[$page_user['user_id']][] = $page_user['target_id'];
            $page_user_message[$page_user['user_id']][] = $page_user['fix_target_flg'];
        }
        return $page_user_message;
    }

    /**
     * ファン一覧全員の取得
     * @param $page_info
     * @param $search_condition
     * @return $fan_list_users
     */
    public function getAllFanList($page_info, $search_condition, $order_condition = array(), $params = '', $isDownLoad = false) {
        /** @var CpCreateSqlService $create_sql_service */
        $create_sql_service = $this->service_factory->create("CpCreateSqlService");
        $list_sql = $create_sql_service->getUserSql($page_info, $search_condition, $order_condition, null, $isDownLoad);

        $db = new aafwDataBuilder();
        list($order, $list_sql) = $this->getOrderBy($search_condition, $order_condition, $list_sql);

        $args = array($params, $order, null, null, 'BrandsUsersRelation');

        $fan_list_users = $db->getBySQL($list_sql, $args);

        return $fan_list_users;
    }

    /**
     * ファン一覧全員の取得用SQL
     * @param $page_info
     * @param $search_condition
     * @param array $order_condition
     * @param bool $isDownLoad
     * @return string
     */
    public function getAllFanListSQL($page_info, $search_condition, $order_condition = array(), $isDownLoad = false) {
        /** @var CpCreateSqlService $create_sql_service */
        $create_sql_service = $this->service_factory->create("CpCreateSqlService");
        $list_sql = $create_sql_service->getUserSql($page_info, $search_condition, $order_condition, null, $isDownLoad);
        list($order, $list_sql) = $this->getOrderBy($search_condition, $order_condition, $list_sql);
        $sql_editor = new aafwSQLEditor();
        $order_sql = $sql_editor->getOrder($order);
        return $list_sql . ' ' . $order_sql;
    }

    /**
     * 画面に表示するファン一覧とその件数の取得
     * @param $page_info
     * @param $search_condition
     * @param array $order_condition
     * @return array
     */
    public function getDisplayFanListAndCount($page_info, $search_condition, $order_condition = array()) {
        /** @var CpCreateSqlService $create_sql_service */
        $create_sql_service = $this->service_factory->create("CpCreateSqlService");
        $list_sql = $create_sql_service->getUserSql($page_info, $search_condition, $order_condition, null, null, array(),true);

        $db = new aafwDataBuilder();
        list($order, $list_sql) = $this->getOrderBy($search_condition, $order_condition, $list_sql);

        $pager = array('count' => $page_info['limit'], 'page' => $page_info['page_no']);
        $args = array('', $order, $pager, null, 'BrandsUsersRelation');

        $fan_list_users = $db->getBySQL($list_sql, $args);

        return $fan_list_users;
    }

    /**
     * 対象ユーザの絞り込み以外に項目毎の絞り込みがあるかどうか
     * @param $search_condition
     * @return $boolean
     */
    public function hasSearchCondition($search_condition) {
        unset($search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE]);
        unset($search_condition[CpCreateSqlService::SEARCH_JOIN_FAN_ONLY]);
        return $search_condition;
    }

    /**
     * 未送信ユーザ情報を取得
     * @param $fan_list_users
     * @param $page_sent_user
     * @param $user_count
     * @return $mixed
     */
    public function getNotSentUserCount($fan_list_users, $page_sent_user, $user_count) {
        $page_sent_user_count = count($page_sent_user);                                        //開いているページ内の送信人数
        $page_not_sent_user_count = count($fan_list_users) - $page_sent_user_count; //開いているページ内の未送信の人数
        $all_not_sent_user_count = $user_count['total_count'] - $user_count['sent_count'];   //全体の未送信人数

        return array($page_not_sent_user_count, $all_not_sent_user_count);
    }

    /**
     * ソートがあった場合のORDER BY句を取得
     * @param $order_condition
     * @param $list_sql
     * @return $mixed
     */
    private function getOrderBy($search_condition, $order_condition, $list_sql) {
        if($order_condition) {
            $order = array();
            if($order_condition[CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'relate.no',
                        'direction' => ' = 0 ASC',
                    ),
                    array(
                        'name' => 'relate.no',
                        'direction' => $direction,
                    )
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'relate.created_at',
                        'direction' => $direction,
                    ),
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'relate.last_login_date',
                        'direction' => ' = "0000-00-00 00:00:00" ASC',
                    ),
                    array(
                        'name' => 'relate.last_login_date',
                        'direction' => $direction,
                    ),
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'relate.login_count',
                        'direction' => $direction,
                    ),
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_PROFILE_AGE]) {
                $list_sql .= ' ORDER BY searchinfo.birthday = "0000-00-00" OR searchinfo.birthday IS NULL ASC,searchinfo.birthday ';
                $list_sql .= $order_condition[CpCreateSqlService::SEARCH_PROFILE_AGE] === self::ORDER_ASC ? ' DESC ' : ' ASC ';
                $list_sql .= ' ,relate.score DESC,relate.no = 0 ASC,relate.no DESC ';
            } elseif($order_condition[CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)',
                        'direction' => $direction,
                    ),
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'ifnull(sumtmp.sum_sa,0)',
                        'direction' => $direction,
                    ),
                );
            } elseif($order_condition[CpCreateSqlService::SEARCH_PROFILE_RATE]) {
                $direction = $order_condition[CpCreateSqlService::SEARCH_PROFILE_RATE] === self::ORDER_ASC ? 'ASC' : 'DESC';
                $order = array(
                    array(
                        'name' => 'relate.rate',
                        'direction' => $direction,
                    ),
                );
            } else {
                $order_key = array_keys($order_condition)[0];
                if (preg_match('/^' . CpCreateSqlService::SEARCH_PROFILE_CONVERSION . '\//', $order_key)) {
                    $conversion_id = explode('/', $order_key)[1];
                    $direction = $order_condition[$order_key] === self::ORDER_ASC ? 'ASC' : 'DESC';
                    $order = array(
                        array(
                            'name' => "cvtmp{$conversion_id}.cnt",
                            'direction' => $direction,
                        ),
                    );
                } elseif(preg_match('/^' . CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT . '\//', $order_key)) {
                    $social_media_type = explode('/', $order_key)[1];
                    if($search_condition[CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT . '/' . $social_media_type]) {
                        $direction = $order_condition[$order_key] === self::ORDER_ASC ? 'ASC' : 'DESC';
                        $order = array(
                            array(
                                'name' => 'sa'.$social_media_type.'.friend_count',
                                'direction' => $direction,
                            ),
                        );
                    } else {
                        $direction = $order_condition[$order_key] === self::ORDER_ASC ? 'ASC' : 'DESC';
                        $order = array(
                            array(
                                'name' => 'sa'.$social_media_type.'.friend_count',
                                'direction' => 'IS NULL',
                            ),
                            array(
                                'name' => 'sa'.$social_media_type.'.friend_count',
                                'direction' => $direction,
                            ),
                        );
                    }
                } elseif($count_column = CpCreateSqlService::$search_count_column[$order_key]) {
                    $direction = $order_condition[$order_key] === self::ORDER_ASC ? 'ASC' : 'DESC';
                    $order = array(
                        array(
                            'name' => 'ifnull(brand_search.'.$count_column.',0)',
                            'direction' => $direction,
                        ),
                    );
                }
            }
        }
        if(!$order_condition[CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO] && !$order_condition[CpCreateSqlService::SEARCH_PROFILE_AGE]) {
            if($order) {
                array_push($order,
                    array(
                        'name' => 'relate.score',
                        'direction' => 'DESC',
                    ),
                    array(
                        'name' => 'relate.no',
                        'direction' => ' = 0 ASC',
                    ),
                    array(
                        'name' => 'relate.no',
                        'direction' => 'DESC',
                    )
                );
            } else {
                $order = array(
                    array(
                        'name' => 'relate.score',
                        'direction' => 'DESC',
                    ),
                    array(
                        'name' => 'relate.no',
                        'direction' => ' = 0 ASC',
                    ),
                    array(
                        'name' => 'relate.no',
                        'direction' => 'DESC',
                    )
                );
            }
        }
        return array($order, $list_sql);
    }

    /**
     * ファン一覧のプロフィールで表示する情報を取得
     * @param $fan_list_users
     * @return array()
     */
    public function getFanListProfile($user_ids, $brand_id, $profile_questions, $conversions, $original_sns_account, $getSocialLikes, $getTwitterFollows, $cp_id = null, $has_comment_option = false, $get_monipla_user_id = null) {
        if(!$user_ids) {
            return;
        }
        $sql = "SELECT
            relate.id brand_user_relation_id
            ,relate.user_id user_id
            ,relate.no no
            ,relate.rate rate
            ,relate.duplicate_address_count shipping_address_duplicate_count
            ,relate.created_at created_at
            ,relate.last_login_date last_login_date
            ,relate.login_count login_count
            ,search_info.sex sex
            ,pref.name pref_name
            ,search_info.birthday birthday
            ,brand_search.cp_entry_count cp_entry_count
            ,brand_search.cp_announce_count cp_announce_count
            ,brand_search.message_delivered_count message_delivered_count
            ,brand_search.message_read_count message_read_count
            ,SA1.id sa1_id, SA1.profile_page_url sa1_profile_page_url, SA1.friend_count sa1_friend_count, SA1.social_media_account_id sa1_uid
            ,SA3.id sa3_id, SA3.profile_page_url sa3_profile_page_url, SA3.friend_count sa3_friend_count, SA3.name sa3_name
            ,SA4.id sa4_id, SA4.profile_page_url sa4_profile_page_url
            ,SA5.id sa5_id, SA5.profile_page_url sa5_profile_page_url
            ,SA7.id sa7_id, SA7.profile_page_url sa7_profile_page_url, SA7.friend_count sa7_friend_count, SA7.name sa7_name
            ,SA8.id sa8_id, SA8.profile_page_url sa8_profile_page_url
            ,relate.personal_info_flg personal_info_flg
            ,relate.optin_flg optin_flg ";

        if($getSocialLikes) {
            $sql .= " ,like_tmp.like_id like_id, like_tmp.social_media_id social_media_id ";
            //連携済FBページのpostに対するいいねやコメント
            $sql .= " ,count_like_tmp.page_uid like_page_uid, count_like_tmp.likes_count ";
            $sql .= " ,count_comment_tmp.page_uid comment_page_uid, count_comment_tmp.comments_count ";
        }

        if($getTwitterFollows) {
            $sql .= " ,twitter_follow_tmp.tw_uid tw_uid, twitter_follow_tmp.social_media_id social_media_id ";

            $sql .= " ,count_retweet_tmp.retweet_tweet_id , count_retweet_tmp.retweets_count ";
            $sql .= " ,count_reply_tmp.reply_tweet_id , count_reply_tmp.replies_count ";
        }

        $original_sns_account_array = array();

        if($original_sns_account) {
            $original_sns_account_array = explode(',',$original_sns_account->content);
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
            $sql .= " ,SA6.id sa6_id, SA6.profile_page_url sa6_profile_page_url ";
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
            $sql .= " ,SA9.id sa9_id, SA9.profile_page_url sa9_profile_page_url ";
        }

        foreach($conversions as $conversion) {
            $alias = "conversion{$conversion->id}";
            $sql .= ",tmp{$alias}.cnt {$alias}";
        }

        if($cp_id) {
            $sql .= " ,cp_users.duplicate_address_count shipping_address_user_duplicate_count ";
        }

        if ($has_comment_option) {
            $sql .= " ,relate.from_id ,cmt_count_tmp.cmt_count";
        }

        if ($get_monipla_user_id) {
            $sql .= " ,users.monipla_user_id ";
        }

        $sql .= " FROM brands_users_relations relate
            LEFT OUTER JOIN user_search_info search_info ON search_info.user_id = relate.user_id AND search_info.del_flg = 0
            LEFT OUTER JOIN shipping_addresses ship ON ship.user_id = relate.user_id AND ship.del_flg = 0
            LEFT OUTER JOIN prefectures pref ON pref.id = ship.pref_id AND pref.del_flg = 0
            LEFT OUTER JOIN brands_users_search_info brand_search ON brand_search.brands_users_relation_id = relate.id AND brand_search.del_flg = 0
            LEFT OUTER JOIN social_accounts SA1 ON SA1.user_id = relate.user_id AND SA1.social_media_id = 1 AND SA1.del_flg = 0
            LEFT OUTER JOIN social_accounts SA3 ON SA3.user_id = relate.user_id AND SA3.social_media_id = 3 AND SA3.del_flg = 0
            LEFT OUTER JOIN social_accounts SA4 ON SA4.user_id = relate.user_id AND SA4.social_media_id = 4 AND SA4.del_flg = 0
            LEFT OUTER JOIN social_accounts SA5 ON SA5.user_id = relate.user_id AND SA5.social_media_id = 5 AND SA5.del_flg = 0
            LEFT OUTER JOIN social_accounts SA7 ON SA7.user_id = relate.user_id AND SA7.social_media_id = 7 AND SA7.del_flg = 0
            LEFT OUTER JOIN social_accounts SA8 ON SA8.user_id = relate.user_id AND SA8.social_media_id = 8 AND SA8.del_flg = 0 ";

        if($getSocialLikes) {
            $sql .= " LEFT OUTER JOIN (SELECT SNS_LIKE.like_id,SNS_LIKE.social_media_id,U.id user_id
            FROM brand_social_accounts BSA
            INNER JOIN social_likes SNS_LIKE ON SNS_LIKE.like_id = BSA.social_media_account_id
            INNER JOIN users U ON U.monipla_user_id = SNS_LIKE.user_id AND U.id IN (".implode(',',$user_ids).")
            WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_FACEBOOK." AND BSA.hidden_flg = 0) like_tmp
            ON like_tmp.user_id = relate.user_id AND like_tmp.user_id = SA1.user_id";

            //連携済FBページのpostに対するいいね

            $sql .= " LEFT OUTER JOIN (
                SELECT SACL.user_id, BSA.social_media_account_id page_uid, SACL.action_count likes_count
                FROM brand_social_accounts BSA
                INNER JOIN sns_action_count_logs SACL ON SACL.social_media_account_id = BSA.social_media_account_id AND SACL.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK." AND SACL.log_type = ".DetailCrawlerUrl::DATA_TYPE_LIKE."
                WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_FACEBOOK." AND BSA.hidden_flg = 0
                GROUP BY SACL.user_id,BSA.id
            ) count_like_tmp
            ON count_like_tmp.user_id = relate.user_id";


            //連携済FBページのpostに対するコメント

             $sql .= " LEFT OUTER JOIN (
                SELECT SACL.user_id, BSA.social_media_account_id page_uid, SACL.action_count comments_count
                FROM brand_social_accounts BSA
                INNER JOIN sns_action_count_logs SACL ON SACL.social_media_account_id = BSA.social_media_account_id AND SACL.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK." AND SACL.log_type = ".DetailCrawlerUrl::DATA_TYPE_COMMENT."
                WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_FACEBOOK." AND BSA.hidden_flg = 0
                GROUP BY SACL.user_id,BSA.id
            ) count_comment_tmp
            ON count_comment_tmp.user_id = relate.user_id";
           
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
            $sql .= " LEFT OUTER JOIN social_accounts SA6 ON SA6.user_id = relate.user_id AND SA6.social_media_id = 6 AND SA6.del_flg = 0 ";
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
            $sql .= " LEFT OUTER JOIN social_accounts SA9 ON SA9.user_id = relate.user_id AND SA9.social_media_id = 9 AND SA9.del_flg = 0 ";
        }

        if($getTwitterFollows) {
            $sql .= " LEFT OUTER JOIN (SELECT BSA.social_media_account_id tw_uid, SA_TMP.social_media_id, SA_TMP.user_id user_id
            FROM brand_social_accounts BSA
            INNER JOIN twitter_streams TS ON TS.brand_social_account_id = BSA.id
            INNER JOIN twitter_follows TL ON TL.stream_id = TS.id
            INNER JOIN social_accounts SA_TMP ON SA_TMP.social_media_account_id = TL.follower_id AND SA_TMP.user_id IN (".implode(',',$user_ids).")
            WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_TWITTER." AND BSA.hidden_flg = 0) twitter_follow_tmp
            ON twitter_follow_tmp.user_id = relate.user_id AND twitter_follow_tmp.user_id = SA3.user_id";


            //連携済TWアカウントのツイートに対するリツイート

            $sql .= " LEFT OUTER JOIN (
                SELECT SACL.user_id, BSA.social_media_account_id retweet_tweet_id, SACL.action_count retweets_count
                FROM brand_social_accounts BSA
                INNER JOIN sns_action_count_logs SACL ON SACL.social_media_account_id = BSA.social_media_account_id AND SACL.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER." AND SACL.log_type = ".DetailCrawlerUrl::DATA_TYPE_RETWEET."
                WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_TWITTER." AND BSA.hidden_flg = 0
                GROUP BY SACL.user_id,BSA.id
            ) count_retweet_tmp
            ON count_retweet_tmp.user_id = relate.user_id ";

            //  連携済TWアカウントのツイートに対するリプライ

            $sql .= " LEFT OUTER JOIN (
                SELECT SACL.user_id, BSA.social_media_account_id reply_tweet_id, SACL.action_count replies_count
                FROM brand_social_accounts BSA
                INNER JOIN sns_action_count_logs SACL ON SACL.social_media_account_id = BSA.social_media_account_id AND SACL.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER." AND SACL.log_type = ".DetailCrawlerUrl::DATA_TYPE_REPLY."
                WHERE BSA.brand_id = {$brand_id} AND BSA.del_flg = 0 AND BSA.social_app_id = ".SocialApps::PROVIDER_TWITTER." AND BSA.hidden_flg = 0
                GROUP BY SACL.user_id,BSA.id
            ) count_reply_tmp
            ON count_reply_tmp.user_id = relate.user_id ";

        }

        foreach($conversions as $conversion) {
            $alias = "conversion{$conversion->id}";
            $relate_alias = "cv_relate{$conversion->id}";
            $sql .= " LEFT OUTER JOIN (SELECT count({$alias}.id) cnt,{$alias}.user_id
                FROM brands_users_conversions {$alias}
                INNER JOIN brands_users_relations {$relate_alias} ON {$relate_alias}.brand_id = {$alias}.brand_id AND {$relate_alias}.user_id = {$alias}.user_id
                WHERE {$alias}.brand_id = {$brand_id} AND {$alias}.user_id IN (".implode(',',$user_ids).") AND {$alias}.del_flg = 0
                AND {$alias}.conversion_id = {$conversion->id} ";

            //TODO ハードコーディング: 特定ブランド以外では、コンバージョン後にブランド会員登録したログはカウントしない
            if ($brand_id != Brand::ANGERS && $brand_id != Brand::CHOJYU) {
                $sql .= " AND {$alias}.date_conversion > {$relate_alias}.created_at ";
            }

            $sql .= " GROUP BY {$alias}.user_id) tmp{$alias} ON tmp{$alias}.user_id = relate.user_id ";
        }

        if($cp_id) {
            $sql .= " LEFT OUTER JOIN cp_users ON relate.user_id = cp_users.user_id AND cp_users.cp_id = {$cp_id} AND cp_users.del_flg = 0 ";
        }

        if ($has_comment_option) {
            $sql .= "LEFT OUTER JOIN (
                SELECT cu_relation.user_id, COUNT(cu_relation.user_id) cmt_count
                FROM (SELECT cp.id, cu.id AS object_id, 1 AS object_type, cu.comment_plugin_id FROM comment_plugins cp LEFT JOIN comment_users cu ON cp.id = cu.comment_plugin_id AND cu.del_flg = 0 WHERE cp.del_flg = 0 AND cp.brand_id = {$brand_id}
                        UNION
                        SELECT cp.id, cur.id AS object_id, 2 AS object_type, cu.comment_plugin_id FROM comment_plugins cp LEFT JOIN comment_users cu ON cp.id = cu.comment_plugin_id AND cu.del_flg = 0 LEFT JOIN comment_user_replies cur ON cur.comment_user_id = cu.id AND cur.del_flg = 0 WHERE cp.del_flg = 0 AND cp.brand_id = {$brand_id}
                    ) cu_data
                    LEFT JOIN comment_user_relations cu_relation ON cu_data.object_id = cu_relation.object_id AND cu_data.object_type = cu_relation.object_type AND cu_relation.del_flg = 0
                WHERE cu_relation.del_flg = 0 AND cu_relation.object_id IS NOT NULL AND cu_relation.discard_flg = 0 AND cu_relation.status = 1
                GROUP BY cu_relation.user_id
            ) cmt_count_tmp
            ON cmt_count_tmp.user_id = relate.user_id";
        }

        if($get_monipla_user_id) {
            $sql .= " LEFT OUTER JOIN users ON relate.user_id = users.id AND users.del_flg = 0 ";
        }

        $sql .= " WHERE relate.user_id IN (".implode(',',$user_ids).") AND relate.brand_id = {$brand_id} ";
        // brands_users_relationsのdel_flgやwithdraw_flgは、取得時に見てるので、ここで見る必要なし。

        $db = new aafwDataBuilder();
        $rs = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));
        if (!$rs) {
            throw new Exception('fan_list_profile not found brand_id='.$brand_id);
        }
        // [ユーザID][会員No]、[ユーザID][設問ID]、[ユーザID][コンバージョンID]のようにデータを持たせる
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $fan_list_profile = array();
        while ($all_profile = $db->fetch($rs)) {
            if(!array_key_exists($all_profile['user_id'], $fan_list_profile)) {
                $fan_list_profile[$all_profile['user_id']]['brand_user_relation_id'] = $all_profile['brand_user_relation_id'];
                $fan_list_profile[$all_profile['user_id']]['no'] = $all_profile['no'];
                $fan_list_profile[$all_profile['user_id']]['rate'] = $all_profile['rate'];
                $fan_list_profile[$all_profile['user_id']]['shipping_address_duplicate_count'] = $all_profile['shipping_address_duplicate_count'];
                $fan_list_profile[$all_profile['user_id']]['optin_flg'] = $all_profile['optin_flg'] == BrandsUsersRelationService::STATUS_OPTIN ? 'ON' : 'OFF';
                $fan_list_profile[$all_profile['user_id']]['history'] = $brands_users_relation_service->getHistorySummary($all_profile['created_at']);
                $fan_list_profile[$all_profile['user_id']]['history_by_datetime'] = $all_profile['created_at'];
                $fan_list_profile[$all_profile['user_id']]['last_login_date'] = $brands_users_relation_service->getLastLoginSummary($all_profile['last_login_date']);
                $fan_list_profile[$all_profile['user_id']]['login_count'] = $all_profile['login_count'];
                $fan_list_profile[$all_profile['user_id']]['sex'] = $all_profile['sex'];
                $fan_list_profile[$all_profile['user_id']]['pref_name'] = $all_profile['pref_name'];
                $fan_list_profile[$all_profile['user_id']]['age'] = $brands_users_relation_service->getAgeFromBirthday($all_profile['birthday']);
                $fan_list_profile[$all_profile['user_id']]['cp_entry_count'] = $all_profile['cp_entry_count'];
                $fan_list_profile[$all_profile['user_id']]['cp_announce_count'] = $all_profile['cp_announce_count'];
                $fan_list_profile[$all_profile['user_id']]['message_delivered_count'] = $all_profile['message_delivered_count'];
                $fan_list_profile[$all_profile['user_id']]['message_read_count'] = $all_profile['message_read_count'];
                if(!$all_profile['message_delivered_count'] || $all_profile['message_delivered_count'] == 0 || !$all_profile['message_read_count'] || $all_profile['message_read_count'] == 0) {
                    $fan_list_profile[$all_profile['user_id']]['message_read_ratio'] = '0%';
                } elseif($all_profile['message_delivered_count'] == $all_profile['message_read_count']) {
                    $fan_list_profile[$all_profile['user_id']]['message_read_ratio'] = '100%';
                } else {
                    $fan_list_profile[$all_profile['user_id']]['message_read_ratio'] = number_format(($all_profile['message_read_count']/$all_profile['message_delivered_count'])*100,1).'%';
                }
                $fan_list_profile[$all_profile['user_id']]['sa1_id'] = $all_profile['sa1_id'];
                $fan_list_profile[$all_profile['user_id']]['sa1_profile_page_url'] = $all_profile['sa1_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa1_friend_count'] = $all_profile['sa1_friend_count'] ? $all_profile['sa1_friend_count'] : 0;
                $fan_list_profile[$all_profile['user_id']]['sa1_uid'] = $all_profile['sa1_uid'];
                $fan_list_profile[$all_profile['user_id']]['sa3_id'] = $all_profile['sa3_id'];
                $fan_list_profile[$all_profile['user_id']]['sa3_profile_page_url'] = $all_profile['sa3_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa3_friend_count'] = $all_profile['sa3_friend_count'] ? $all_profile['sa3_friend_count'] : 0;
                $fan_list_profile[$all_profile['user_id']]['sa3_name'] = $all_profile['sa3_name'];
                $fan_list_profile[$all_profile['user_id']]['sa4_id'] = $all_profile['sa4_id'];
                $fan_list_profile[$all_profile['user_id']]['sa4_profile_page_url'] = $all_profile['sa4_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa5_id'] = $all_profile['sa5_id'];
                $fan_list_profile[$all_profile['user_id']]['sa5_profile_page_url'] = $all_profile['sa5_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa6_id'] = $all_profile['sa6_id'];
                $fan_list_profile[$all_profile['user_id']]['sa6_profile_page_url'] = $all_profile['sa6_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa7_id'] = $all_profile['sa7_id'];
                $fan_list_profile[$all_profile['user_id']]['sa7_profile_page_url'] = $all_profile['sa7_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa7_friend_count'] = $all_profile['sa7_friend_count'] ? $all_profile['sa7_friend_count'] : 0;
                $fan_list_profile[$all_profile['user_id']]['sa7_name'] = $all_profile['sa7_name'];
                $fan_list_profile[$all_profile['user_id']]['sa8_id'] = $all_profile['sa8_id'];
                $fan_list_profile[$all_profile['user_id']]['sa8_profile_page_url'] = $all_profile['sa8_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['sa9_id'] = $all_profile['sa9_id'];
                $fan_list_profile[$all_profile['user_id']]['sa9_profile_page_url'] = $all_profile['sa9_profile_page_url'];
                $fan_list_profile[$all_profile['user_id']]['profile_questionnaire_status'] = $brands_users_relation_service->getProfileQuestionnaireStatus($all_profile['personal_info_flg']);
            }
            if($getSocialLikes) {
                $fan_list_profile[$all_profile['user_id']]['social_media_id'][$all_profile['like_id']] = $all_profile['social_media_id'];
                $fan_list_profile[$all_profile['user_id']]['like_id'][$all_profile['like_id']] = $all_profile['like_id'];

                //連携済FBページのpostに対するいいねやコメント
                $fan_list_profile[$all_profile['user_id']]['likes_count'][$all_profile['like_page_uid']]  = $all_profile['likes_count'];
                $fan_list_profile[$all_profile['user_id']]['comments_count'][$all_profile['comment_page_uid']]  = $all_profile['comments_count'];
            }
            if($getTwitterFollows) {
                $fan_list_profile[$all_profile['user_id']]['social_media_id'][$all_profile['tw_uid']] = $all_profile['social_media_id'];
                $fan_list_profile[$all_profile['user_id']]['tw_uid'][$all_profile['tw_uid']] = $all_profile['tw_uid'];

                //連携済TWアカウントのツイートに対するリツイートやリプライ
                $fan_list_profile[$all_profile['user_id']]['retweets_count'][$all_profile['retweet_tweet_id']]  = $all_profile['retweets_count'];
                $fan_list_profile[$all_profile['user_id']]['replies_count'][$all_profile['reply_tweet_id']]  = $all_profile['replies_count'];
            }
            foreach($conversions as $conversion) {
                $fan_list_profile[$all_profile['user_id']]['conversion'.$conversion->id] = $all_profile['conversion'.$conversion->id] ? $all_profile['conversion'.$conversion->id] : 0;
            }
            if($cp_id) {
                $fan_list_profile[$all_profile['user_id']]['shipping_address_user_duplicate_count'] = $all_profile['shipping_address_user_duplicate_count'];
            }

            if ($has_comment_option) {
                $fan_list_profile[$all_profile['user_id']]['from_id'] = $all_profile['from_id'];
                $fan_list_profile[$all_profile['user_id']]['cmt_count'] = $all_profile['cmt_count'];
            }

            if($get_monipla_user_id) {
                $fan_list_profile[$all_profile['user_id']]['monipla_user_id'] = $all_profile['monipla_user_id'];
            }
        }

        // プロフィールアンケートは1問ずつ別で取得する
        foreach($profile_questions as $relation_id => $profile_question) {
            if($profile_question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                $questionnaire_sql = "SELECT R.user_id user_id, ANS.answer_text answer_text FROM brands_users_relations R
                    LEFT OUTER JOIN profile_question_free_answers ANS ON ANS.brands_users_relation_id = R.id AND ANS.del_flg = 0
                    AND ANS.questionnaires_questions_relation_id = {$relation_id}
                    WHERE R.user_id IN (".implode(',',$user_ids).") AND R.brand_id = {$brand_id}";
            } else {
                $questionnaire_sql = "SELECT R.user_id user_id, CH.choice choice, ANS.answer_text answer_text FROM brands_users_relations R
                    LEFT OUTER JOIN profile_question_choice_answers ANS ON ANS.brands_users_relation_id = R.id AND ANS.del_flg = 0
                    AND ANS.questionnaires_questions_relation_id = {$relation_id}
                    LEFT OUTER JOIN profile_question_choices CH ON CH.id = ANS.choice_id AND CH.del_flg = 0
                    WHERE R.user_id IN (".implode(',',$user_ids).") AND R.brand_id = {$brand_id}";
            }
            $rs = $db->getBySQL($questionnaire_sql, array(array('__NOFETCH__' => true)));
            if (!$rs) {
                throw new Exception('profile_question_answer not found relation_id='.$relation_id);
            }

            while ($question_answer = $db->fetch($rs)) {
                if($profile_question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                    // 複数選択の設問の場合は連結させる必要がある。
                    if(isset($fan_list_profile[$question_answer['user_id']]['question_'.$profile_question->id])) {
                        if($question_answer['answer_text'] || $question_answer['answer_text'] === '0') {
                            $fan_list_profile[$question_answer['user_id']]['question_'.$profile_question->id] .= ",その他(".$question_answer['answer_text'].")";
                        } else {
                            $fan_list_profile[$question_answer['user_id']]['question_'.$profile_question->id] .= ",".$question_answer['choice'];
                        }
                    } else {
                        if ($question_answer['answer_text'] || $question_answer['answer_text'] === '0') {
                            $fan_list_profile[$question_answer['user_id']]['question_' . $profile_question->id] = "その他(" . $question_answer['answer_text'] . ")";
                        } else {
                            $fan_list_profile[$question_answer['user_id']]['question_' . $profile_question->id] = $question_answer['choice'];
                        }
                    }
                }
                if($profile_question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                    $fan_list_profile[$question_answer['user_id']]['question_'.$profile_question->id] = $question_answer['answer_text'];
                }
                if($profile_question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $fan_list_profile[$question_answer['user_id']]['question_'.$profile_question->id] = $question_answer['choice'];
                }
            }
        }

        return $fan_list_profile;
    }

    public function getFanListProfileForActionDataDownLoad($user_ids, $brand_id, $action_data) {
        if (!$user_ids) {
            return;
        }

        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');

        $sql = "SELECT
            relate.id brand_user_relation_id
            ,relate.user_id user_id
            ,relate.no no
            ,relate.rate rate
            ,search_info.sex sex
            ,pref.name pref_name
            ,search_info.birthday birthday
            ,SA1.id sa1_id, SA1.profile_page_url sa1_profile_page_url, SA1.friend_count sa1_friend_count
            ,SA3.id sa3_id, SA3.profile_page_url sa3_profile_page_url, SA3.friend_count sa3_friend_count
            ,SA4.id sa4_id, SA4.profile_page_url sa4_profile_page_url
            ,SA5.id sa5_id, SA5.profile_page_url sa5_profile_page_url
            ,SA7.id sa7_id, SA7.profile_page_url sa7_profile_page_url, SA7.friend_count sa7_friend_count
            ,SA8.id sa8_id, SA8.profile_page_url sa8_profile_page_url ";


        $original_sns_account_array = array();

        if ($action_data['original_sns_account']) {
            $original_sns_account_array = explode(',',$action_data['original_sns_account']->content);
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
            $sql .= " ,SA6.id sa6_id, SA6.profile_page_url sa6_profile_page_url ";
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
            $sql .= " ,SA9.id sa9_id, SA9.profile_page_url sa9_profile_page_url ";
        }

        $sql .= " FROM brands_users_relations relate
            LEFT OUTER JOIN user_search_info search_info ON search_info.user_id = relate.user_id AND search_info.del_flg = 0
            LEFT OUTER JOIN shipping_addresses ship ON ship.user_id = relate.user_id AND ship.del_flg = 0
            LEFT OUTER JOIN prefectures pref ON pref.id = ship.pref_id AND pref.del_flg = 0
            LEFT OUTER JOIN brands_users_search_info brand_search ON brand_search.brands_users_relation_id = relate.id AND brand_search.del_flg = 0
            LEFT OUTER JOIN social_accounts SA1 ON SA1.user_id = relate.user_id AND SA1.social_media_id = 1 AND SA1.del_flg = 0
            LEFT OUTER JOIN social_accounts SA3 ON SA3.user_id = relate.user_id AND SA3.social_media_id = 3 AND SA3.del_flg = 0
            LEFT OUTER JOIN social_accounts SA4 ON SA4.user_id = relate.user_id AND SA4.social_media_id = 4 AND SA4.del_flg = 0
            LEFT OUTER JOIN social_accounts SA5 ON SA5.user_id = relate.user_id AND SA5.social_media_id = 5 AND SA5.del_flg = 0
            LEFT OUTER JOIN social_accounts SA7 ON SA7.user_id = relate.user_id AND SA7.social_media_id = 7 AND SA7.del_flg = 0
            LEFT OUTER JOIN social_accounts SA8 ON SA8.user_id = relate.user_id AND SA8.social_media_id = 8 AND SA8.del_flg = 0 ";



        if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
            $sql .= " LEFT OUTER JOIN social_accounts SA6 ON SA6.user_id = relate.user_id AND SA6.social_media_id = 6 AND SA6.del_flg = 0 ";
        }

        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
            $sql .= " LEFT OUTER JOIN social_accounts SA9 ON SA9.user_id = relate.user_id AND SA9.social_media_id = 9 AND SA9.del_flg = 0 ";
        }


        $sql .= " WHERE relate.user_id IN (".implode(',',$user_ids).") AND relate.brand_id = {$brand_id} ";

        $db = aafwDataBuilder::newBuilder();
        $rs = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));
        if (!$rs) {
            throw new Exception('getFanListProfileForActionDataDownLoad failed. brand_id='.$brand_id);
        }

        $data = array();
        while ($fan_profile = $db->fetch($rs)) {
            $data[$fan_profile['user_id']]['no'] = $fan_profile['no'] ?: '-';
            if ($fan_profile['rate'] == BrandsUsersRelationService::BLOCK) {
                $data[$fan_profile['user_id']]['rate'] = 'ブロック';
            } elseif($fan_profile['rate'] == BrandsUsersRelationService::NON_RATE) {
                $data[$fan_profile['user_id']]['rate'] = '未評価';
            } else {
                $data[$fan_profile['user_id']]['rate'] = '+' . $fan_profile['rate'];
            }

            if($action_data['page_settings']->privacy_required_sex) {
                if ($fan_profile['sex'] == 'm') {
                    $data[$fan_profile['user_id']]['sex'] = '男性';
                } elseif ($fan_profile['sex'] == 'f') {
                    $data[$fan_profile['user_id']]['sex'] = '女性';
                } else {
                    $data[$fan_profile['user_id']]['sex'] = '';
                }
            } else {
                $data[$fan_profile['user_id']]['sex'] = '';
            }

            if($action_data['page_settings']->privacy_required_birthday) {
                $data[$fan_profile['user_id']]['age'] = $brands_users_relation_service->getAgeFromBirthday($fan_profile['birthday']);
            } else {
                $data[$fan_profile['user_id']]['age'] = '';
            }

            if($action_data['page_settings']->privacy_required_address) {
                $data[$fan_profile['user_id']]['pref_name'] = $fan_profile['pref_name'];
            } else {
                $data[$fan_profile['user_id']]['pref_name'] = '';
            }

            $data[$fan_profile['user_id']]['sa1_profile_page_url'] = $fan_profile['sa1_id'] ? '◯' : '';
            $data[$fan_profile['user_id']]['sa1_friend_count'] = $fan_profile['sa1_id'] ? $fan_profile['sa1_friend_count'] : '';
            $data[$fan_profile['user_id']]['sa3_profile_page_url'] = $fan_profile['sa3_id'] ? '◯' : '';
            $data[$fan_profile['user_id']]['sa3_friend_count'] = $fan_profile['sa3_id'] ? $fan_profile['sa3_friend_count'] : '';
            $data[$fan_profile['user_id']]['sa8_profile_page_url'] = $fan_profile['sa8_id'] ? '◯' : '';
            $data[$fan_profile['user_id']]['sa7_profile_page_url'] = $fan_profile['sa7_id'] ? '◯' : '';
            $data[$fan_profile['user_id']]['sa7_friend_count'] = $fan_profile['sa7_id'] ? $fan_profile['sa7_friend_count'] : '';
            $data[$fan_profile['user_id']]['sa5_profile_page_url'] = $fan_profile['sa5_id'] ? '◯' : '';
            $data[$fan_profile['user_id']]['sa4_profile_page_url'] = $fan_profile['sa4_id'] ? '◯' : '';

            if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                $data[$fan_profile['user_id']]['sa6_profile_page_url'] = $fan_profile['sa6_id'] ? '◯' : '';
            }

            if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                $data[$fan_profile['user_id']]['sa9_profile_page_url'] = $fan_profile['sa9_id'] ? '◯' : '';
            }
        }
        return $data;
    }

    public function getFanListStatus($cp_user_ids, $cp_action, $isFanList) {
        if(!$cp_user_ids) {
            return;
        }

        $fan_list_statuses = array();
        $db = new aafwDataBuilder();

        $sql = "SELECT S.cp_action_id cp_action_id, S.cp_user_id cp_user_id, S.status status, M.read_flg read_flg, CU.referrer referrer, CU.from_id fid, S.updated_at updated_at, S.created_at created_at";
        if($cp_action->type == CpAction::TYPE_PHOTO) {
            $sql .= ",pu.id pu_id, pu.photo_url photo_url ";
        } elseif ($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
            $sql .= ",iw.join_count join_count ";
        } elseif ($cp_action->type == CpAction::TYPE_COUPON) {
            $sql .= ",code.code code ";
        } elseif ($cp_action->type == CpAction::TYPE_FREE_ANSWER) {
            $sql .= ",fa.free_answer free_answer ";
        }

        $sql .= " FROM cp_users CU
                LEFT OUTER JOIN cp_user_action_statuses S ON S.cp_user_id = CU.id AND S.cp_action_id = ".$cp_action->id." AND S.del_flg = 0
                LEFT OUTER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.cp_action_id = ".$cp_action->id." AND M.del_flg = 0 ";

        if ($cp_action->type == CpAction::TYPE_PHOTO) {
            $sql .= " LEFT OUTER JOIN photo_users pu ON pu.cp_action_id = M.cp_action_id AND pu.cp_user_id = M.cp_user_id AND pu.del_flg = 0 ";
        } elseif ($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
            $sql .= " LEFT OUTER JOIN instant_win_users iw ON iw.cp_action_id = M.cp_action_id AND iw.cp_user_id = M.cp_user_id AND iw.del_flg = 0 ";
        } elseif ($cp_action->type == CpAction::TYPE_COUPON) {
            $sql .= "LEFT OUTER JOIN coupon_code_users ccu ON ccu.cp_action_id = M.cp_action_id AND ccu.user_id = CU.user_id AND CU.del_flg = 0
                LEFT OUTER JOIN coupon_codes code ON code.id = ccu.coupon_code_id AND code.del_flg = 0";
        } elseif ($cp_action->type == CpAction::TYPE_FREE_ANSWER) {
            $sql .= "LEFT OUTER JOIN cp_free_answer_action_answers fa ON fa.cp_action_id = M.cp_action_id AND fa.cp_user_id = M.cp_user_id AND fa.del_flg = 0";
        }

        $sql .= " WHERE CU.id IN (" . implode(',', $cp_user_ids) . ") AND CU.del_flg = 0 ";

        $rs = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));

        while ($user_status = $db->fetch($rs)) {
            if(!$user_status['cp_user_id']) {
                continue;
            }
            // 日付を使用する用途
            // order_no = 1 => 画面側のエントリーモジュールの完了日
            // order_no = 2 => csvダウンロードの参加日時
            // order_no = 最後 => 画面側の参加完了モジュールの完了日、csvダウンロードの参加完了日時
            if($user_status['status']) {
                $fan_list_statuses[$user_status['cp_user_id']]['finish_day'] = $user_status['updated_at'] ? date('Y/m/d', strtotime($user_status['updated_at'])) : '';
                $fan_list_statuses[$user_status['cp_user_id']]['finish_time'] = $user_status['updated_at'] ? date('H:i', strtotime($user_status['updated_at'])) : '';
            }
            if($cp_action->order_no == 2) {
                $fan_list_statuses[$user_status['cp_user_id']]['entry_day'] = $user_status['created_at'] ? date('Y/m/d', strtotime($user_status['created_at'])) : '';
                $fan_list_statuses[$user_status['cp_user_id']]['entry_time'] = $user_status['created_at'] ? date('H:i', strtotime($user_status['created_at'])) : '';
            }
            $fan_list_statuses[$user_status['cp_user_id']]['status'] = $isFanList ? $this->getActionStatus($user_status['read_flg'], $user_status['status']) : $this->getActionDisplayStatus($cp_action, $user_status);
            $fan_list_statuses[$user_status['cp_user_id']]['referrer'] = $user_status['referrer'];
            $fan_list_statuses[$user_status['cp_user_id']]['fid'] = $user_status['fid'];
        }
        return $fan_list_statuses;
    }

    public function getFanListStatusForActionDataDownLoad($cp_user_ids, $cp_action) {
        if(!$cp_user_ids) {
            return;
        }

        $db = new aafwDataBuilder();

        $sql = "SELECT
            S.cp_user_id cp_user_id,
            S.status status,
            M.read_flg read_flg,
            S.updated_at updated_at";

        $sql .= " FROM cp_users CU
                LEFT OUTER JOIN cp_user_action_statuses S ON S.cp_user_id = CU.id AND S.cp_action_id = ".$cp_action->id." AND S.del_flg = 0
                LEFT OUTER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.cp_action_id = ".$cp_action->id." AND M.del_flg = 0 ";

        $sql .= " WHERE CU.id IN (" . implode(',', $cp_user_ids) . ") AND CU.del_flg = 0 ";

        $rs = $db->getBySQL($sql, array(array('__NOFETCH__' => true)));

        $fan_list_status = array();
        while ($user_status = $db->fetch($rs)) {
            if(!$user_status['cp_user_id']) {
                continue;
            }
            $fan_list_status[$user_status['cp_user_id']]['status'] = $this->getActionStatus($user_status['read_flg'], $user_status['status']);
            $fan_list_status[$user_status['cp_user_id']]['day'] = $user_status['status'] == CpUserActionStatus::JOIN ? date('Y/m/d', strtotime($user_status['updated_at'])) : '';
            $fan_list_status[$user_status['cp_user_id']]['time'] = $user_status['status'] == CpUserActionStatus::JOIN ? date('H:i', strtotime($user_status['updated_at'])) : '';
        }
        return $fan_list_status;
    }

    public function getActionStatus($read_flg = null, $status = null) {
        if ($status == CpUserActionStatus::JOIN) {
            return '完了';
        } elseif ($read_flg == CpUserActionMessage::STATUS_READ) {
            return '既読';
        } elseif ($read_flg == 0) {
            return '未読';
        } else {
            return '';
        }
    }

    public function getActionDisplayStatus($cp_action, $user_status) {
        // 完了
        if($user_status['status'] == CpUserActionStatus::JOIN) {
            if($cp_action->type == CpAction::TYPE_PHOTO) {
                $displayStatus[0] = $user_status['photo_url'];
                $displayStatus[1] = $user_status['pu_id'];
            } elseif($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
                $displayStatus = CpUserActionStatus::STATUS_WIN.'（参加数:' . $user_status['join_count'] . ')';
            } elseif($cp_action->type == CpAction::TYPE_COUPON) {
                if($user_status['code']) {
                    $displayStatus = CpUserActionStatus::STATUS_WIN.'（コード:' . $user_status['code'] . '）';
                } else {
                    $displayStatus = CpUserActionStatus::STATUS_WIN;
                }
            } elseif($cp_action->type == CpAction::TYPE_FREE_ANSWER) {
                $displayStatus = CpUserActionStatus::STATUS_FINISH . '（' . $user_status['free_answer'] . '）';
            } elseif($cp_action->isOpeningCpAction()) {
                $displayStatus[0] = CpUserActionStatus::STATUS_FINISH;
                $displayStatus[1] = '参加日:'.date('Y/m/d', strtotime($user_status['updated_at']));
            } elseif($cp_action->type == CpAction::TYPE_ANNOUNCE_DELIVERY) {
                $displayStatus = CpUserActionStatus::STATUS_ANNOUNCE_DELIVERED;
            } else {
                $displayStatus = CpUserActionStatus::STATUS_FINISH;
            }
        // 参加条件外
        } elseif ($user_status['status'] == CpUserActionStatus::CAN_NOT_JOIN) {
            $displayStatus = CpUserActionStatus::STATUS_REJECTED;
        // 既読
        } elseif($user_status['read_flg'] == CpUserActionMessage::STATUS_READ) {
            if($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
                $displayStatus = CpUserActionStatus::STATUS_LOSE.'（参加数:' . $user_status['join_count'] . ')';
            } else {
                $displayStatus = CpUserActionStatus::STATUS_READ;
            }
        // 未読
        } elseif($user_status['read_flg'] == CpUserActionMessage::STATUS_UNREAD) {
            $displayStatus = CpUserActionStatus::STATUS_UNREAD;
        } else {
            $displayStatus = CpUserActionStatus::STATUS_UNSENT;
        }
        return $displayStatus;
    }

    public function getFanListSendTime($fan_list_users, $cp_action_id) {
        if(!$fan_list_users) {
            return;
        }

        foreach($fan_list_users as $fan_list_user) {
            $user_ids[] = $fan_list_user->user_id;
        }
        $sql = "SELECT user_id, updated_at FROM cp_message_delivery_targets
        WHERE status = ".CpMessageDeliveryTarget::STATUS_DELIVERED." AND del_flg = 0 AND cp_action_id = ". $cp_action_id ." AND user_id in (".implode(',',$user_ids).")";
        $db = new aafwDataBuilder();
        $fan_list_send_times = $db->getBySQL($sql, array());
        $fan_list_send_time_array = array();
        foreach($fan_list_send_times as $send_time) {
            $fan_list_send_time_array[$send_time['user_id']] = $send_time['updated_at'];
        }
        return $fan_list_send_time_array;
    }

    public function getFanListQuestion($user_ids, $questions, $brand_id) {
        if(!$user_ids) {
            return;
        }

        $db = new aafwDataBuilder();
        $fan_list_questions = array();

        foreach($questions as $relation_id => $question) {

            if($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                $questionnaire_sql = "SELECT R.user_id user_id, ANS.answer_text answer_text FROM brands_users_relations R
                    LEFT OUTER JOIN question_free_answers ANS ON ANS.brands_users_relation_id = R.id AND ANS.del_flg = 0
                    AND ANS.questionnaires_questions_relation_id = {$relation_id} AND ANS.question_id = {$question->id}
                    WHERE R.user_id IN (".implode(',',$user_ids).") AND R.brand_id = {$brand_id}";
            } else {
                $questionnaire_sql = "SELECT R.user_id user_id, CH.choice choice, ANS.answer_text answer_text FROM brands_users_relations R
                    LEFT OUTER JOIN question_choice_answers ANS ON ANS.brands_users_relation_id = R.id AND ANS.del_flg = 0
                    AND ANS.questionnaires_questions_relation_id = {$relation_id} AND ANS.question_id = {$question->id}
                    LEFT OUTER JOIN question_choices CH ON CH.id = ANS.choice_id AND CH.del_flg = 0
                    WHERE R.user_id IN (".implode(',',$user_ids).") AND R.brand_id = {$brand_id}";
            }

            $question_answers = $db->getBySQL($questionnaire_sql, array());
            if (!$question_answers) {
                throw new Exception('question_answer not found relation_id='.$relation_id);
            }

            foreach($question_answers as $question_answer) {

                if($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                    // 複数選択の設問の場合は連結させる必要がある。
                    if(isset($fan_list_questions[$question_answer['user_id']][$question->id])) {
                        if($question_answer['answer_text'] || $question_answer['answer_text'] === '0') {
                            $fan_list_questions[$question_answer['user_id']][$question->id] .= ",その他(".$question_answer['answer_text'].")";
                        } else {
                            $fan_list_questions[$question_answer['user_id']][$question->id] .= ",".$question_answer['choice'];
                        }
                    } else {
                        if ($question_answer['answer_text'] || $question_answer['answer_text'] === '0') {
                            $fan_list_questions[$question_answer['user_id']][$question->id] = "その他(" . $question_answer['answer_text'] . ")";
                        } else {
                            $fan_list_questions[$question_answer['user_id']][$question->id] = $question_answer['choice'];
                        }
                    }
                }

                if($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                    $fan_list_questions[$question_answer['user_id']][$question->id] = $question_answer['answer_text'];
                }

                if($question->type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                    // 複数選択の設問の場合は連結させる必要がある。
                    if($fan_list_questions[$question_answer['user_id']][$question->id]) {
                        $fan_list_questions[$question_answer['user_id']][$question->id] .= ",".$question_answer['choice'];
                    } else {
                        $fan_list_questions[$question_answer['user_id']][$question->id] = $question_answer['choice'];
                    }
                }

                if($question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $fan_list_questions[$question_answer['user_id']][$question->id] = $question_answer['choice'];
                }
            }
        }
        return $fan_list_questions;
    }

    public function getFanListInstagramHashtag($cp_action_id, $cp_user_ids) {
        if(!$cp_user_ids) {
            return;
        }

        $sql = "SELECT I.cp_user_id cp_user_id,
                I.instagram_user_name user_name,
                I.duplicate_flg duplicate_flg,
                I.created_at created_at,
                P.id p_id,
                P.link link,
                P.thumbnail thumbnail,
                P.detail_data detail_data,
                P.standard_resolution standard_resolution,
                P.approval_status approval_status,
                P.reverse_post_time_flg reverse_post_time_flg
                FROM instagram_hashtag_users I
                LEFT OUTER JOIN instagram_hashtag_user_posts P ON I.id = P.instagram_hashtag_user_id AND P.del_flg = 0
                WHERE I.cp_action_id = {$cp_action_id} AND I.del_flg = 0 AND I.cp_user_id IN (".implode(',',$cp_user_ids).")
                ORDER BY p_id ASC";
        $user_hashtag = array();
        $db = new aafwDataBuilder();
        $args = array(array(), array(), array(), false, 'InstagramHashtagUserPost');
        $hashtags = $db->getBySQL($sql, $args);
        foreach ($hashtags as $hashtag) {
            $user_hashtag[$hashtag->cp_user_id]['user_name'] = $hashtag->user_name;
            $user_hashtag[$hashtag->cp_user_id]['created_at'] = $hashtag->created_at;
            $user_hashtag[$hashtag->cp_user_id]['link'][] = $hashtag->link;
            $user_hashtag[$hashtag->cp_user_id]['thumbnail'][] = $hashtag->thumbnail;
            $user_hashtag[$hashtag->cp_user_id]['id'][] = $hashtag->p_id;
            $user_hashtag[$hashtag->cp_user_id]['duplicate_flg'] = $hashtag->duplicate_flg;
            $user_hashtag[$hashtag->cp_user_id]['post_text'][] = json_decode($hashtag->detail_data)->caption->text;
            $user_hashtag[$hashtag->cp_user_id]['reverse_post_time'][] = $hashtag->getReversePostTimeStatus();
            $user_hashtag[$hashtag->cp_user_id]['approval_status'][] = $hashtag->getApprovalStatus();
            $user_hashtag[$hashtag->cp_user_id]['approval_status_status'][] = $hashtag->approval_status;
            $user_hashtag[$hashtag->cp_user_id]['post_date_time'][] = date('Y/m/d H:i', json_decode($hashtag->detail_data)->created_time ? json_decode($hashtag->detail_data)->created_time : '');
        }
        return $user_hashtag;
    }

    public function getPhotoFanListUser ($cp_action_id, $cp_user_ids, $needUserPhotoShare) {
        if(!$cp_user_ids) {
            return;
        }

        $db = new aafwDataBuilder();
        $user_photo_array = array();
        $sql = "SELECT P.cp_user_id
                ,P.id
                ,P.photo_url
                ,P.photo_title
                ,P.photo_comment
                ,P.approval_status";

        if($needUserPhotoShare) {
            $sql .= ",S.social_media_type
                    ,S.share_text";
        }

        $sql .= " FROM photo_users P";

        if($needUserPhotoShare) {
            $sql .= " LEFT OUTER JOIN photo_user_shares S ON P.id = S.photo_user_id AND S.del_flg = 0 AND S.execute_status = ".MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS;
        }
        $sql .= " WHERE P.cp_action_id = {$cp_action_id} AND P.del_flg = 0 AND P.cp_user_id IN (".implode(',',$cp_user_ids).")";
        $args = array(array(), array(), array(), false, 'PhotoUser');
        $photos = $db->getBySQL($sql, $args);
        foreach ($photos as $photo) {
            $user_photo_array[$photo->cp_user_id]['id'] = $photo->id;
            $user_photo_array[$photo->cp_user_id]['photo_url'] = $photo->photo_url;
            $user_photo_array[$photo->cp_user_id]['photo_title'] = $photo->photo_title;
            $user_photo_array[$photo->cp_user_id]['photo_comment'] = $photo->photo_comment;
            $user_photo_array[$photo->cp_user_id]['approval_status'] = $photo->getApprovalStatus();
            if($needUserPhotoShare && $photo->social_media_type) {
                $user_photo_array[$photo->cp_user_id]['social_media_type'][] = $photo->social_media_type;
                $user_photo_array[$photo->cp_user_id]['share_text'] = $photo->share_text;
            }
        }
        return $user_photo_array;
    }

    public function getGiftSenderFanList ($brand_id, $concrete_action_id, $has_address = 0) {
        $db = new aafwDataBuilder();
        $sql = "SELECT DISTINCT
                       BUR.user_id sender_id
                     , BUR.no member_no
                     , GM.receiver_user_id receiver_id
                     , GM.updated_at updated_at";
        if ($has_address) {
            $sql .= ", SA.first_name
                     , SA.last_name
                     , SA.first_name_kana
                     , SA.last_name_kana
                     , SA.zip_code1
                     , SA.zip_code2
                     , P.name address0
                     , SA.address1
                     , SA.address2
                     , SA.address3
                     , SA.tel_no1
                     , SA.tel_no2
                     , SA.tel_no3";
        }
        $sql .= " FROM gift_messages GM
                LEFT JOIN cp_users CU ON CU.id = GM.cp_user_id AND CU.del_flg = 0
                LEFT JOIN brands_users_relations BUR ON BUR.user_id = CU.user_id AND BUR.brand_id = {$brand_id} AND BUR.del_flg = 0 AND BUR.withdraw_flg = 0";
        if ($has_address) {
            $sql .= " LEFT JOIN shipping_addresses SA ON SA.user_id = GM.receiver_user_id AND SA.del_flg = 0
                      LEFT JOIN prefectures P ON P.id = SA.pref_id AND P.del_flg = 0";
        }
        $sql .= " WHERE GM.del_flg = 0
                AND GM.cp_gift_action_id = {$concrete_action_id}
                AND GM.receiver_user_id > 0";
        $args = array(array(), array(), array(), false, 'GiftMessage');
        $gift_message = $db->getBySQL($sql, $args);
        $user_gift_messages = array();

        if ($has_address) {
            foreach ($gift_message as $element) {
                $user_gift_messages[$element->receiver_id][] = array(
                    'no'                => $element->member_no,
                    'updated_at'        => $element->updated_at,
                    'first_name'        => $element->first_name,
                    'last_name'         => $element->last_name,
                    'first_name_kana'   => $element->first_name_kana,
                    'last_name_kana'    => $element->last_name_kana,
                    'zip_code'          => $element->zip_code1 ? $element->zip_code1 . '-' . $element->zip_code2 : '',
                    'address0'          => $element->address0,
                    'address1'          => $element->address1,
                    'address2'          => $element->address2,
                    'address3'          => $element->address3,
                    'tel_no'            => $element->tel_no1 ? $element->tel_no1 . '-' . $element->tel_no2 . '-' . $element->tel_no3 : '',
                );
            }
        } else {
            foreach ($gift_message as $element) {
                $user_gift_messages[$element->receiver_id][] = array(
                    'no'            => $element->member_no,
                    'updated_at'    => $element->updated_at
                );
            }
        }

        return $user_gift_messages;
    }

    public function getGiftFanList($cp_user_ids, $concrete_action_id, $brand_id) {
        if (!$cp_user_ids) {
            return;
        }

        $db = aafwDataBuilder::newBuilder();
        $sql = "SELECT DISTINCT
                       GM.cp_user_id cp_user_id
                     , BUR.no receiver_no
                     , GM.image_url image_url";

        $sql .= " FROM gift_messages GM
                LEFT JOIN brands_users_relations BUR ON BUR.user_id = GM.receiver_user_id AND BUR.brand_id = {$brand_id} AND BUR.del_flg = 0 AND BUR.withdraw_flg = 0";

        $sql .= " WHERE GM.del_flg = 0
                AND GM.cp_user_id IN (".implode(',',$cp_user_ids).")
                AND GM.cp_gift_action_id = {$concrete_action_id}
                AND GM.receiver_user_id > 0";
        $args = array(array(), array(), array(), false, 'GiftMessage');
        $gift_messages = $db->getBySQL($sql, $args);
        $user_gift_array = array();

        foreach ($gift_messages as $gift_message) {
            $user_gift_array[$gift_message->cp_user_id] = array(
                'receiver_no' => $gift_message->receiver_no,
                'image_url'   => $gift_message->image_url
            );
        }

        return $user_gift_array;
    }

    public function getFanListPopularVote($cp_action_id, $cp_user_ids, $canShare) {
        if(!$cp_user_ids) {
            return;
        }

        $db = aafwDataBuilder::newBuilder();
        $user_popular_vote_array = array();

        $sql = "SELECT P.cp_user_id
                ,C.title
                ";
        if ($canShare) {
            $sql .= "
                ,SFB.social_media_type fb_share
                ,SFB.share_text fb_share_text
                ,STW.social_media_type tw_share
                ,STW.share_text tw_share_text
                ";
        }

        $sql .= " FROM popular_vote_users P";
        $sql .= " INNER JOIN cp_popular_vote_candidates C ON P.cp_popular_vote_candidate_id = C.id";
        if ($canShare) {
            $sql .= " LEFT JOIN popular_vote_user_shares SFB ON P.id = SFB.popular_vote_user_id AND SFB.del_flg = 0 AND SFB.social_media_type = " . SocialAccount::SOCIAL_MEDIA_FACEBOOK . " AND SFB.execute_status = ".MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS;
            $sql .= " LEFT JOIN popular_vote_user_shares STW ON P.id = STW.popular_vote_user_id AND STW.del_flg = 0 AND STW.social_media_type = " . SocialAccount::SOCIAL_MEDIA_TWITTER . " AND STW.execute_status = ".MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS;
        }
        $sql .= " WHERE P.cp_action_id = {$cp_action_id} AND P.del_flg = 0 AND P.cp_user_id IN (".implode(',',$cp_user_ids).")";

        $args = array(array(), array(), array(), false, 'PopularVoteUser');
        $votes = $db->getBySQL($sql, $args);

        if ($canShare) {
            foreach ($votes as $vote) {
                $user_popular_vote_array[$vote->cp_user_id]['title'] = $vote->title;
                $user_popular_vote_array[$vote->cp_user_id]['fb_share'] = $vote->fb_share ? 'シェア' : '';
                $user_popular_vote_array[$vote->cp_user_id]['tw_share'] = $vote->tw_share ? 'シェア' : '';
                $user_popular_vote_array[$vote->cp_user_id]['share_text'] = $vote->fb_share_text ? : $vote->tw_share_text;
            }
        } else {
            foreach ($votes as $vote) {
                $user_popular_vote_array[$vote->cp_user_id]['title'] = $vote->title;
            }
        }
        return $user_popular_vote_array;
    }

    public function getFanListCoupon($user_ids, $cp_action_id) {
        if(!$user_ids) {
            return;
        }

        $db = aafwDataBuilder::newBuilder();
        $user_coupon_array = array();

        $sql = "SELECT U.user_id
                ,C.code
                ";

        $sql .= " FROM coupon_code_users U";
        $sql .= " INNER JOIN coupon_codes C ON U.coupon_code_id = C.id AND C.del_flg = 0";

        $sql .= " WHERE U.cp_action_id = {$cp_action_id} AND U.del_flg = 0 AND U.user_id IN (".implode(',',$user_ids).")";

        $args = array(array(), array(), array(), false, 'CouponCodeUser');
        $coupons = $db->getBySQL($sql, $args);

        foreach ($coupons as $coupon) {
            $user_coupon_array[$coupon->user_id]['code'] = $coupon->code;
        }

        return $user_coupon_array;
    }

    public function getFanListCodeAuth($user_ids, $cp_action_id) {
        if(!$user_ids) {
            return;
        }

        $db = aafwDataBuilder::newBuilder();
        $user_code_auth_array = array();

        $sql = "SELECT U.user_id
                ,C.code
                ,U.used_date
                ";

        $sql .= " FROM code_authentication_users U";
        $sql .= " INNER JOIN code_authentication_codes C ON U.code_auth_code_id = C.id AND C.del_flg = 0";

        $sql .= " WHERE U.cp_action_id = {$cp_action_id} AND U.del_flg = 0 AND U.user_id IN (".implode(',',$user_ids).")";

        $args = array(array(), array(), array(), false, 'CodeAuthenticationUser');
        $code_auths = $db->getBySQL($sql, $args);

        foreach ($code_auths as $code_auth) {
            $user_code_auth_array[$code_auth->user_id]['code'][] = $code_auth->code;
            $user_code_auth_array[$code_auth->user_id]['used_date'][] = $code_auth->used_date;
        }

        return $user_code_auth_array;
    }

    public function getFanListTweet($cp_user_ids, $cp_tweet_action_id) {
        if(!$cp_user_ids) {
            return;
        }

        $db = aafwDataBuilder::newBuilder();
        $user_tweet_array = array();

        $sql = "SELECT M.cp_user_id
                ,M.skipped
                ,M.tweet_text
                ,M.tweet_content_url
                ,M.tweet_status
                ,M.approval_status
                ,P.image_url
                ";

        $sql .= " FROM tweet_messages M";
        $sql .= " LEFT OUTER JOIN tweet_photos P ON M.id = P.tweet_message_id AND P.del_flg = 0";

        $sql .= " WHERE M.cp_tweet_action_id = {$cp_tweet_action_id} AND M.del_flg = 0 AND M.cp_user_id IN (".implode(',',$cp_user_ids).")";

        $args = array(array(), array(), array(), false, 'CodeAuthenticationUser');
        $tweets = $db->getBySQL($sql, $args);

        foreach ($tweets as $tweet) {
            if (!$user_tweet_array[$tweet->cp_user_id]) {
                $user_tweet_array[$tweet->cp_user_id]['status_string'] = $tweet->skipped ? 'スキップ' : 'ツイート済';
                $user_tweet_array[$tweet->cp_user_id]['tweet_content_url'] = $tweet->tweet_content_url;
                $user_tweet_array[$tweet->cp_user_id]['tweet_text'] = $tweet->tweet_text;
                $user_tweet_array[$tweet->cp_user_id]['tweet_status'] = TweetMessage::getStaticTweetStatus($tweet->tweet_status);
                $user_tweet_array[$tweet->cp_user_id]['approval_status'] = TweetMessage::getStaticApprovalStatus($tweet->approval_status);
            }
            $user_tweet_array[$tweet->cp_user_id]['image_url'][] = $tweet->image_url;
        }

        return $user_tweet_array;
    }

    public function getFanlistAttribute($user_ids, $definitions) {
        if(!$user_ids || !$definitions) {
            return;
        }

        $user_attributes = array();
        foreach($definitions as $def) {
            $attributes = $this->brand_service->getAssignableCustomAttributeValueByUserIds($user_ids, $def);
            if(!$attributes) {
                continue;
            }
            // 現状はタイプは1つしか使用していない
            if($def->attribute_type === BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET) {
                foreach($attributes as $attr) {
                    $value = $def->convertValueByValueSet($attr->value);
                    $user_attributes[$def->id][$attr->user_id] = $value ?: '';
                }
            }
        }
        return $user_attributes;
    }
}