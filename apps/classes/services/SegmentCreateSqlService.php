<?php
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');

class SegmentCreateSqlService extends CpCreateSqlService {

    /**
     * FROM句を返す
     * @param $page_info
     * @param $search_condition
     * @param $order_condition
     * @param null $count_sql
     * @param $isDownLoad
     * @param $extern_columns
     * @return string
     */
    protected function getFromSql($page_info, $search_condition, $order_condition, $count_sql = null, $isDownLoad, $extern_columns) {

        foreach ($search_condition as $key => $condition) {
            if (!preg_match('/^segmenting_condition_/', $key)) {
                continue;
            }

            foreach ($condition as $sub_key => $sub_value) {
                if (!isset($search_condition[$sub_key])) {
                    $search_condition[$sub_key] = $sub_value;
                }
            }
        }

        return parent::getFromSql($page_info, $search_condition, $order_condition, $count_sql, $isDownLoad, $extern_columns);
    }

    /**
     * WHERE句を返す
     * @param $brand_id
     * @param $search_conditions
     * @param bool $is_or_condition
     * @return string
     */
    protected function getWhereSql($brand_id, $search_conditions, $is_or_condition = false) {
        $where_sql = " WHERE relate.brand_id = {$this->escape($brand_id)} AND relate.del_flg = 0 AND relate.withdraw_flg = 0 ";

        $where_clause = $this->getWhereClauseBySearchCondition($search_conditions);
        if ($where_clause != "") {
            $where_sql .= " AND " . $where_clause;
        }

        return $where_sql;
    }

    /**
     * @param $search_conditions
     * @param bool $is_or_condition
     * @return string
     */
    protected function getWhereClauseBySearchCondition($search_conditions, $is_or_condition = false) {
        $where_clause = "";
        $where_clauses = array();
        $conjunction = $is_or_condition ? " OR " : " AND ";

        foreach ($search_conditions as $key => $value) {
            if (preg_match('/^segmenting_condition_/', $key)) {
                $temp_clause = $this->getWhereClauseBySearchCondition($value, true);
            } else {
                $temp_clause = $this->getWhereCondition($key, $value, $search_conditions);
            }

            if ($temp_clause != "") {
                $not_condition = $this->isNegativeExpression($value) ? " NOT " : "";
                $where_clauses[] = $not_condition . "(" . $temp_clause . ")";
            }
        }

        if (count($where_clauses) != 0) {
            $where_clause = implode($conjunction, $where_clauses);
        }

        return $where_clause;
    }

    /**
     * @param $search_key
     * @param $search_condition
     * @param $search_conditions
     * @return string
     */
    protected function getWhereCondition($search_key, $search_condition, $search_conditions) {
        $where_clause = "";
        list($search_type, $search_sub_key) = $this->parseSearchKey($search_key);

        switch ($search_type) {
            case self::SEARCH_PROFILE_RATE:
                $where_clause = $this->getSearchRateWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_MEMBER_NO:
                $where_clause = $this->getSearchMemberNoWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_REGISTER_PERIOD:
                $where_clause = $this->getSearchRegisterPeriodWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_SOCIAL_ACCOUNT:
                $where_clause = $this->getSearchSocialAccountWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SOCIAL_ACCOUNT_SUM:
                $where_clause = $this->getSearchSocialAccountSumWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_LAST_LOGIN:
                $where_clause = $this->getSearchLastLoginWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_LOGIN_COUNT:
                $where_clause = $this->getSearchLoginCountWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_SEX:
                $where_clause = $this->getSearchSexWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_AGE:
                $where_clause = $this->getSearchAgeWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_ADDRESS:
                $where_clause = $this->getSearchAddressWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_CONVERSION:
                $where_clause = $this->getSearchConversionWhereClause($search_condition);
                break;
            case self::SEARCH_CP_ENTRY_COUNT:
            case self::SEARCH_CP_ANNOUNCE_COUNT:
            case self::SEARCH_MESSAGE_DELIVERED_COUNT:
            case self::SEARCH_MESSAGE_READ_COUNT:
                $where_clause = $this->getSearchCountColumnCountWhereClause($search_condition, $search_type);
                break;
            case self::SEARCH_MESSAGE_READ_RATIO:
                $where_clause = $this->getSearchMessageReadRatioWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE:
                $where_clause = $this->getSearchProfileQuestionnaireWhereClause($search_condition);
                break;
            case self::SEARCH_IMPORT_VALUE:
                $where_clause = $this->getSearchImportValueWhereClause($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS:
                $where_clause = $this->getSearchQuestionnaireStatusWhereClause($search_condition);
                break;
            case self::SEARCH_PARTICIPATE_CONDITION:
                $where_clause = $this->getSearchParticipateConditionWhereClause($search_condition);
                break;
            case self::SEARCH_QUESTIONNAIRE:
                $where_clause = $this->getSearchQuestionnaireWhereClause($search_condition);
                break;
            case self::SEARCH_DELIVERY_TIME:
                $where_clause = $this->getSearchDeliveryTimeWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_QUERY_USER_TYPE:
                $where_clause = $this->getSearchQueryUserWhereClause($search_condition);
                break;
            case self::SEARCH_JOIN_FAN_ONLY:
                $where_clause = $this->getJoinUserOnlyWhereClause($search_condition);
                break;
            case self::SEARCH_PHOTO_SHARE_SNS:
                $where_clause = $this->getSearchPhotoShareSnsWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_SHARE_TEXT:
                $where_clause = $this->getSearchPhotoShareTextWhereClause($search_condition, $search_sub_key, $search_conditions);
                break;
            case self::SEARCH_PHOTO_APPROVAL_STATUS:
                $where_clause = $this->getSearchPhotoApprovalStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SHARE_TYPE:
                $where_clause = $this->getSearchShareUserLogTypeWhereClause($search_condition);
                break;
            case self::SEARCH_SHARE_TEXT:
                $where_clause = $this->getSearchShareUserLogTextWhereClause($search_condition);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION:
                $where_clause = $this->getSearchInstagramHashtagDuplicateWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME:
                $where_clause = $this->getSearchInstagramHashtagReverseWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS:
                $where_clause = $this->getSearchInstagramHashtagApprovalStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_FB_LIKE_TYPE:
                $where_clause = $this->getSearchFbLikeLogStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_TW_FOLLOW_TYPE:
                $where_clause = $this->getSearchTwFollowLogStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_TWEET_TYPE:
                $where_clause = $this->getSearchTweetMessageStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION:
                $where_clause = $this->getSearchYoutubeChannelApprovalStatusWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_GIFT_RECEIVER_FAN:
                $where_clause = $this->getSearchGiftReceiverFanWhereClause($search_condition);
                break;
            case self::SEARCH_POPULAR_VOTE_CANDIDATE:
                $where_clause = $this->getSearchPopularVoteCandidateWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_SNS:
                $where_clause = $this->getSearchPopularVoteShareSnsWhereClause($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_TEXT:
                $where_clause = $this->getSearchPopularVoteShareTextWhereClause($search_condition, $search_sub_key, $search_conditions);
                break;
            case self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE:
                $where_clause = $this->getSearchSocialAccountInteractiveWhereClause($search_condition);
                break;
            case self::SEARCH_DUPLICATE_ADDRESS:
                $where_clause = $this->getSearchDuplicateAddressWhereClause($search_condition);
                break;
        }

        return trim($where_clause);
    }

    /**
     * 評価に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchRateWhereClause($condition) {
        $first_flg = true;
        $where_sql = "";

        foreach ($condition as $key => $value) {
            if (preg_match('/^search_rate\//', $key)) {
                $status = explode('/', $key)[1];
                if ($first_flg) {
                    $where_sql .= "relate.rate = {$status} ";
                    $first_flg = false;
                } else {
                    $where_sql .= " OR relate.rate = {$status} ";
                }
            }
        }

        return $where_sql;
    }

    /**
     * 会員番号に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchMemberNoWhereClause($condition) {
        $member_no_from = $this->escape($condition['search_profile_member_no_from']);

        // カンマが含まれているか判定(数値かどうかの判定はvalidateで行うので不要)
        if (preg_match("/,/", $member_no_from)) {
            $where_sql = "relate.no IN ({$member_no_from})";
        } else {
            $where_sql = "relate.no = {$member_no_from}";
        }

        return $where_sql;
    }

    /**
     * 登録期間に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchRegisterPeriodWhereClause($condition) {
        if (!$condition['search_profile_register_period_to']) {
            $where_sql = "relate.created_at >= '{$this->getFromDateFormat($condition['search_profile_register_period_from'])}'";
        } elseif (!$condition['search_profile_register_period_from']) {
            $where_sql = "relate.created_at <= '{$this->getToDateFormat($condition['search_profile_register_period_to'])}'";
        } else {
            $where_sql = "relate.created_at BETWEEN '{$this->getFromDateFormat($condition['search_profile_register_period_from'])}' AND '{$this->getToDateFormat($condition['search_profile_register_period_to'])}'";
        }

        return $where_sql;
    }

    /**
     * 連携済SNSに関する絞り込み
     * @param $condition
     * @param $media_type
     * @return string
     */
    protected function getSearchSocialAccountWhereClause($condition, $media_type) {
        // 連携・未連携の絞り込み
        $where_sql = "";
        if ($condition['search_social_account/' . $media_type . '/' . self::LINK_SNS]) {
            $where_sql .= " sa{$media_type}.id IS NOT NULL ";
        }
        if ($condition['search_social_account/' . $media_type . '/' . self::NOT_LINK_SNS]) {
            if ($condition['search_social_account/' . $media_type . '/' . self::LINK_SNS]) {
                $where_sql .= " OR sa{$media_type}.id IS NULL ";
            } else {
                $where_sql .= " sa{$media_type}.id IS NULL ";
            }
        }

        // 友達数の絞り込み
        $from_count = isset($condition['search_friend_count_from/' . $media_type]) ? $condition['search_friend_count_from/' . $media_type] : "";
        $to_count = isset($condition['search_friend_count_to/' . $media_type]) ? $condition['search_friend_count_to/' . $media_type] : "";

        if ($from_count === '' && $to_count === '') {
            return $where_sql;
        }
        $where_sql = "(" . $where_sql . ")";

        if ($from_count === '' && $to_count !== '') {
            $where_sql .= " AND ( sa{$media_type}.friend_count <= {$this->escape($to_count)} ";
            if ($to_count == 0) {
                $where_sql .= " OR sa{$media_type}.friend_count IS NULL ";
            }
            $where_sql .= " ) ";
        } elseif ($from_count !== '' && $to_count === '') {
            $where_sql .= " AND ( sa{$media_type}.friend_count >= {$this->escape($from_count)} ";
            if ($from_count == 0) {
                $where_sql .= " OR sa{$media_type}.friend_count IS NULL ";
            }
            $where_sql .= " ) ";

        } elseif ($from_count !== '' && $to_count !== '') {
            $where_sql .= " AND ( sa{$media_type}.friend_count BETWEEN {$this->escape($from_count)} AND {$this->escape($to_count)} ";
            if ($from_count == 0) {
                $where_sql .= " OR sa{$media_type}.friend_count IS NULL ";
            }
            $where_sql .= " ) ";
        }

        return $where_sql;
    }

    /**
     * 連携済SNSの合計に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchSocialAccountSumWhereClause($condition) {

        $friend_sum_from = isset($condition['search_friend_count_sum_from']) ? $condition['search_friend_count_sum_from'] : "";
        $friend_sum_to = isset($condition['search_friend_count_sum_to']) ? $condition['search_friend_count_sum_to'] : "";

        if ($friend_sum_from !== '' && $friend_sum_to !== '') {
            $friend_sum_where_sql = "ifnull(sumtmp.sum_sa,0) BETWEEN {$friend_sum_from} AND {$friend_sum_to}";
        } elseif ($friend_sum_from !== '') {
            $friend_sum_where_sql = "ifnull(sumtmp.sum_sa,0) >= {$friend_sum_from}";
        } elseif ($friend_sum_to !== '') {
            $friend_sum_where_sql = "ifnull(sumtmp.sum_sa,0) <= {$friend_sum_to}";
        } else {
            $friend_sum_where_sql = "";
        }


        $link_sum_from = isset($condition['search_link_sns_count_from']) ? $condition['search_link_sns_count_from'] : "";
        $link_sum_to = isset($condition['search_link_sns_count_to']) ? $condition['search_link_sns_count_to'] : "";

        if ($link_sum_from !== '' && $link_sum_to !== '') {
            $link_sum_where_sql = "ifnull(sumtmp.cnt_sa,0) BETWEEN {$link_sum_from} AND {$link_sum_to}";
        } elseif ($link_sum_from !== '') {
            $link_sum_where_sql = "ifnull(sumtmp.cnt_sa,0) >= {$link_sum_from}";
        } elseif ($link_sum_to !== '') {
            $link_sum_where_sql = "ifnull(sumtmp.cnt_sa,0) <= {$link_sum_to}";
        } else {
            $link_sum_where_sql = "";
        }

        if ($friend_sum_where_sql !== "" && $link_sum_where_sql !== "") {
            $where_sql = "(" . $friend_sum_where_sql . ") AND (" . $link_sum_where_sql . ")";
        } elseif ($friend_sum_where_sql !== "") {
            $where_sql = $friend_sum_where_sql;
        } elseif ($link_sum_where_sql !== "") {
            $where_sql = $link_sum_where_sql;
        } else {
            $where_sql = "";
        }

        return $where_sql;
    }

    /**
     * 最終ログインに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchLastLoginWhereClause($condition) {
        if (!$condition['search_profile_last_login_to']) {
            $where_sql = "relate.last_login_date >= '{$this->getFromDateTimeFormat($condition['search_profile_last_login_from'])}'";
        } elseif (!$condition['search_profile_last_login_from']) {
            $where_sql = "relate.last_login_date <= '{$this->getToDateTimeFormat($condition['search_profile_last_login_to'])}'";
        } else {
            $where_sql = "relate.last_login_date BETWEEN '{$this->getFromDateTimeFormat($condition['search_profile_last_login_from'])}' AND '{$this->getToDateTimeFormat($condition['search_profile_last_login_to'])}'";
        }

        return $where_sql;
    }

    /**
     * ログイン回数に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchLoginCountWhereClause($condition) {
        // 範囲選択のチェックがついている場合
        if (is_null($condition['search_profile_login_count_to']) || $condition['search_profile_login_count_to'] === '') { //toはpostされないこともあるのでnullのチェックも必要
            $where_sql = "relate.login_count >= {$this->escape($condition['search_profile_login_count_from'])}";
        } elseif ($condition['search_profile_login_count_from'] === '') { //fromはpostされないことはない
            $where_sql = "relate.login_count <= {$this->escape($condition['search_profile_login_count_to'])}";
        } else {
            $where_sql = "relate.login_count BETWEEN {$this->escape($condition['search_profile_login_count_from'])} AND {$this->escape($condition['search_profile_login_count_to'])}";
        }

        return $where_sql;
    }

    /**
     * 性別に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchSexWhereClause($condition) {
        $first_flg = true;
        $where_sql = "";

        foreach ($condition as $key => $value) {
            if (preg_match('/^search_profile_sex\//', $key)) {
                $sex = explode('/', $key)[1];
                if ($first_flg) {
                    if ($sex == UserAttributeService::ATTRIBUTE_SEX_UNKWOWN) {
                        $where_sql .= " ( searchinfo.sex IS NULL OR searchinfo.sex = '' ) ";
                    } else {
                        $where_sql .= " searchinfo.sex = '{$this->escape($sex)}' ";
                    }
                } else {
                    if ($sex == UserAttributeService::ATTRIBUTE_SEX_UNKWOWN) {
                        $where_sql .= " OR ( searchinfo.sex IS NULL OR searchinfo.sex = '' ) ";
                    } else {
                        $where_sql .= " OR searchinfo.sex = '{$this->escape($sex)}' ";
                    }
                }
            }
            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * 年齢に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchAgeWhereClause($condition) {

        if ($condition['search_profile_age_to'] !== '' && $condition['search_profile_age_from'] !== '') {
            $birthday_to = $this->getBirthdayByAge($condition['search_profile_age_to'] + 1);
            $birthday_from = $this->getBirthdayByAge($condition['search_profile_age_from']);
            $where_sql = "searchinfo.birthday BETWEEN '{$birthday_to}' AND '{$birthday_from}' ";
        } elseif ($condition['search_profile_age_from'] !== '') {
            $birthday_from = $this->getBirthdayByAge($condition['search_profile_age_from']);
            $where_sql = "searchinfo.birthday BETWEEN '1900-00-00' AND '{$birthday_from}' ";
        } elseif ($condition['search_profile_age_to'] !== '') {
            $birthday_to = $this->getBirthdayByAge($condition['search_profile_age_to'] + 1);
            $where_sql = "searchinfo.birthday >= '{$birthday_to}' ";
        } else {
            $where_sql = "";
        }

        if ($condition['search_profile_age_not_set']) {
            if ($where_sql !== "") {
                $where_sql .= " OR ";
            }

            $where_sql .= "(searchinfo.birthday IS NULL OR searchinfo.birthday = '0000-00-00')";
        }

        return $where_sql;
    }

    /**
     * 住所に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchAddressWhereClause($condition) {
        $first_flg = true;
        $where_sql = "";

        foreach ($condition as $key => $value) {
            // TODO: continue on preg_match
            if (preg_match('/^search_profile_address\//', $key)) {
                $prefecture_id = explode('/', $key)[1];
                if ($first_flg) {
                    if ($prefecture_id == self::NOT_SET_PREFECTURE) {
                        $where_sql .= " ( addr.pref_id IS NULL OR addr.pref_id = '' OR addr.pref_id = 0 ) ";
                    } else {
                        $where_sql .= " addr.pref_id = {$this->escape($prefecture_id)} ";
                    }
                } else {
                    if ($prefecture_id == self::NOT_SET_PREFECTURE) {
                        $where_sql .= " OR ( addr.pref_id IS NULL OR addr.pref_id = '' OR addr.pref_id = 0 ) ";
                    } else {
                        $where_sql .= " OR addr.pref_id = {$this->escape($prefecture_id)} ";
                    }
                }
            }
            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * TODO need check
     * コンバージョンに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchConversionWhereClause($condition) {
        $current_key = key($condition);
        if (!preg_match('/^search_profile_conversion_/', $current_key)) {
            return "";
        }

        $conversion_id = $this->escape(explode('/', $current_key)[1]);
        $search_conversion_from = $condition['search_profile_conversion_from/' . $conversion_id];
        $search_conversion_to = $condition['search_profile_conversion_to/' . $conversion_id];

        if ($search_conversion_from == 0 && $search_conversion_to === '') { // 検索条件が「0〜」の場合は絞り込みをする必要なし(全員対象になる)
            $where_sql = "";
        } elseif ($search_conversion_from == 0 && $search_conversion_to == 0) { // 検索条件が「0〜0」の場合はコンバージョンテーブルに存在しない人だけを対象にすれば良い
            $where_sql = "cv{$conversion_id}.user_id IS NULL ";
        } else { // 検索条件が「1〜100」等の場合
            if ($search_conversion_from == 0) {
                $where_sql = "";
            } else {
                $where_sql = "cvtmp{$conversion_id}.user_id IS NOT NULL ";
            }
        }

        return $where_sql;
    }

    /**
     * キャンペーン参加回数・キャンペーン当選数・メッセージ受信数・メッセージ開封数に関する絞り込み
     * @param $search_type
     * @param $condition
     * @return string
     */
    protected function getSearchCountColumnCountWhereClause($condition, $search_type) {
        $count_item = self::$search_count_item[$search_type];
        $count_column = self::$search_count_column[$search_type];

        if ($condition[$count_item . '_to'] !== '' && $condition[$count_item . '_from'] !== '') {
            $where_sql = "brand_search.{$count_column} BETWEEN {$this->escape($condition[$count_item . '_from'])} AND {$this->escape($condition[$count_item . '_to'])}";

            if ($condition[$count_item . '_from'] == 0) {
                $where_sql = "(" . $where_sql . " OR brand_search.{$count_column} IS NULL)";
            }
        } elseif ($condition[$count_item . '_from'] !== '') {
            $where_sql = "brand_search.{$count_column} >= {$this->escape($condition[$count_item . '_from'])}";

            if ($condition[$count_item . '_from'] == 0) {
                $where_sql = "(" . $where_sql . " OR brand_search.{$count_column} IS NULL)";
            }
        } elseif ($condition[$count_item . '_to'] !== '') {
            $where_sql = "brand_search.{$count_column} <= {$this->escape($condition[$count_item . '_to'])}";

            if ($condition[$count_item . '_to'] == 0) {
                $where_sql = "(" . $where_sql . " OR brand_search.{$count_column} IS NULL)";
            }
        } else {
            $where_sql = "";
        }

        return $where_sql;
    }

    /**
     * メッセージ開封率に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchMessageReadRatioWhereClause($condition) {
        if ($condition['search_message_ratio_to'] === '' && $condition['search_message_ratio_from'] !== '') {
            if ($condition['search_message_ratio_from'] == 0) {
                $where_sql = "";
            } else {
                $where_sql = "ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100 >= {$this->escape($condition['search_message_ratio_from'])}";
            }
        } elseif ($condition['search_message_ratio_to'] !== '' && $condition['search_message_ratio_from'] === '') {
            // TODO not supported while ratio_to = 0??
            if ($condition['search_message_ratio_to'] == 0) {
                $where_sql = "";
            } else {
                $where_sql = "(ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100 <= {$this->escape($condition['search_message_ratio_to'])} OR brand_search.message_read_count IS NULL)";
            }
        } else {
            $where_sql = "ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100
                    BETWEEN {$this->escape($condition['search_message_ratio_from'])} AND {$this->escape($condition['search_message_ratio_to'])} ";

            if ($condition['search_message_ratio_from'] == 0) {
                $where_sql = "(" . $where_sql . " OR brand_search.message_read_count IS NULL)";
            }
        }

        return $where_sql;
    }

    /**
     * 参加時アンケートに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchProfileQuestionnaireWhereClause($condition) {
        $where_sql = "";
        $first_flg = true;
        $switch_value = '';
        $have_free_ans_condition_flg = false;

        foreach ($condition as $key => $value) {
            if (!preg_match('/^search_profile_questionnaire\//', $key)) {
                continue;
            }

            $split_key = explode('/', $key);
            $relate_id = $split_key[1];
            $user_answer = $split_key[2];

            if ($condition['questionnaire_type/' . $relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                $search_free_ans_sql = '';
                if (preg_match('/^search_profile_questionnaire\/' . $relate_id . '\/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE . '/', $key)) {
                    $search_free_ans_sql .= 'free_ans' . $relate_id . '.answer_text IS NULL OR free_ans' . $relate_id . '.answer_text = "" ';
                } else {
                    $search_free_ans_sql .= 'free_ans' . $relate_id . '.answer_text IS NOT NULL AND free_ans' . $relate_id . '.answer_text != "" ';
                }

                if ($first_flg) {
                    $where_sql .= $search_free_ans_sql;
                    $first_flg = false;
                } else {
                    if ($have_free_ans_condition_flg) {
                        $where_sql .= " OR ( " . $search_free_ans_sql . ")";
                    } else {
                        $where_sql .= " AND ( " . $search_free_ans_sql;
                    }
                }
                $have_free_ans_condition_flg = true;
                continue;
            }

            if (!$switch_value) $switch_value = $condition['switch_type/' . self::SEARCH_PROFILE_QUESTIONNAIRE . '/' . $relate_id] ? $condition['switch_type/' . self::SEARCH_PROFILE_QUESTIONNAIRE . '/' . $relate_id] : self::QUERY_TYPE_OR;

            if ($switch_value == self::QUERY_TYPE_AND) {
                if ($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                    if ($first_flg) {
                        $where_sql .= "( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                    } else {
                        $where_sql .= " AND ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                    }
                } else {
                    if ($first_flg) {
                        $where_sql .= "ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                    } else {
                        $where_sql .= " AND ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                    }
                }
            } else {
                if ($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                    if ($first_flg) {
                        $where_sql .= "( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                    } else {
                        $where_sql .= " OR ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                    }
                } else {
                    if ($first_flg) {
                        $where_sql .= "ans{$relate_id}.choice_id = {$user_answer} ";
                    } else {
                        $where_sql .= " OR ans{$relate_id}.choice_id = {$user_answer} ";
                    }
                }
            }
            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * 外部インポートデータに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchImportValueWhereClause($condition) {
        $where_sql = "";
        $first_flg = true;

        foreach ($condition as $key => $value) {
            if (!preg_match('/^search_import_value\//', $key)) {
                continue;
            }

            $split_key = explode('/', $key);
            $definition_id = $split_key[1];
            $value = $split_key[2];

            // TODO fix or conditions in other functions
            if (!$first_flg) {
                $where_sql .= " OR ";
            }

            if ($value == self::NOT_SET_VALUE) {
                $where_sql .= " ( bua{$definition_id}.id = '' OR bua{$definition_id}.id IS NULL ) ";
            } else {
                $where_sql .= " bua{$definition_id}.value = {$value} ";
            }

            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * アンケート参加状況に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchQuestionnaireStatusWhereClause($condition) {
        $first_flg = true;
        $where_sql = "";

        foreach ($condition as $key => $value) {
            if (preg_match('/^search_questionnaire_status\//', $key)) {
                $status = explode('/', $key)[1];

                if ($first_flg) {
                    $where_sql .= "relate.personal_info_flg = {$status} ";
                    $first_flg = false;
                } else {
                    $where_sql .= " OR relate.personal_info_flg = {$status} ";
                }
            }
        }

        return $where_sql;
    }

    /**
     * 参加状況に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchParticipateConditionWhereClause($condition) {
        $where_sql = "";
        $first_flg = true;
        $conjunction = " OR ";

        foreach ($condition as $key => $value) {
            $action_id = explode('/', $key)[1];
            $participate_status = explode('/', $key)[2];

            if ($first_flg) {
                // スピードくじのときだけ、ANDとORの選択がある
                $switch_value = $condition['switch_type/' . self::SEARCH_PARTICIPATE_CONDITION . '/' . $action_id] ? $condition['switch_type/' . self::SEARCH_PARTICIPATE_CONDITION . '/' . $action_id] : '';
                if ($switch_value) {
                    $conjunction = $this->getConjunction($switch_value);
                }
            }
            if ($switch_value == self::QUERY_TYPE_AND) {
                $alias = $this->escape($action_id) . '_' . $this->escape($participate_status);
            } else {
                $alias = $this->escape($action_id);
            }
            if ($participate_status == self::PARTICIPATE_COMPLETE) {
                $status = CpUserActionStatus::JOIN;

                $where_sql .= $first_flg ? "" : " {$conjunction}";
                // switch_valueがあるときスピードくじと判断
                if ($switch_value) {
                    $where_sql .= " instant{$alias}.prize_status = " . InstantWinUsers::PRIZE_STATUS_WIN . " ";
                } else {
                    $where_sql .= " state{$alias}.status = {$status} ";
                }
            }
            if ($participate_status == self::PARTICIPATE_REJECTED) {
                $status = CpUserActionStatus::CAN_NOT_JOIN;
                if ($first_flg) {
                    $where_sql .= " state{$alias}.status = {$status} ";
                } else {
                    $where_sql .= " {$conjunction} state{$alias}.status = {$status} ";
                }
            }
            if ($participate_status == self::PARTICIPATE_READ) {
                $status = CpUserActionStatus::NOT_JOIN;
                $read_flg = CpUserActionMessage::STATUS_READ;
                if ($first_flg) {
                    $where_sql .= " ( state{$alias}.status = {$status} AND mes{$alias}.read_flg = {$read_flg} ) ";
                } else {
                    $where_sql .= " {$conjunction} ( state{$alias}.status = {$status} AND mes{$alias}.read_flg = {$read_flg} ) ";
                }
            }
            if ($participate_status == self::PARTICIPATE_NOT_READ) {
                $read_flg = CpUserActionMessage::STATUS_UNREAD;
                if ($first_flg) {
                    $where_sql .= " mes{$alias}.read_flg = {$read_flg} ";
                } else {
                    $where_sql .= " {$conjunction} mes{$alias}.read_flg = {$read_flg} ";
                }
            }
            if ($participate_status == self::PARTICIPATE_NOT_SEND) {
                if ($first_flg) {
                    $where_sql .= " mes{$alias}.id IS NULL ";
                } else {
                    $where_sql .= " {$conjunction} mes{$alias}.id IS NULL ";
                }
            }
            if ($participate_status == self::PARTICIPATE_COUNT_INSTANT_WIN) {
                if (!$first_flg) {
                    $where_sql .= " {$conjunction} ";
                }
                if ($condition['search_count_instant_win_from/' . $action_id] === '' && $condition['search_count_instant_win_to/' . $action_id] !== '') {
                    $escape_count_to = $this->escape($condition['search_count_instant_win_to/' . $action_id]);
                    $where_sql .= " ( instant{$alias}.join_count <= {$escape_count_to} OR instant{$alias}.join_count IS NULL ) ";
                } elseif ($condition['search_count_instant_win_from/' . $action_id] !== '' && $condition['search_count_instant_win_to/' . $action_id] === '') {
                    $escape_count_from = $this->escape($condition['search_count_instant_win_from/' . $action_id]);
                    $where_sql .= " instant{$alias}.join_count >= {$escape_count_from} ";
                } elseif ($condition['search_count_instant_win_from/' . $action_id] !== '' && $condition['search_count_instant_win_to/' . $action_id] !== '') {
                    $escape_count_from = $this->escape($condition['search_count_instant_win_from/' . $action_id]);
                    $escape_count_to = $this->escape($condition['search_count_instant_win_to/' . $action_id]);
                    $where_sql .= " instant{$alias}.join_count BETWEEN {$escape_count_from} AND {$escape_count_to} ";
                }
            }
            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * アンケートに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchQuestionnaireWhereClause($condition) {
        $where_sql = "";
        $first_flg = true;
        $switch_value = '';
        $have_free_ans_condition_flg = false;

        foreach ($condition as $key => $value) {
            if (!preg_match('/^search_questionnaire\//', $key)) {
                continue;
            }

            $split_key = explode('/', $key);
            $relate_id = $split_key[1];
            $user_answer = $split_key[2];

            if ($condition['questionnaire_type/' . $relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                $search_free_ans_sql = '';
                if (preg_match('/^search_questionnaire\/' . $relate_id . '\/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE . '/', $key)) {
                    $search_free_ans_sql .= " q_free_ans{$relate_id}.answer_text IS NULL ";
                } else {
                    $search_free_ans_sql .= " q_free_ans{$relate_id}.answer_text IS NOT NULL ";
                }
                if ($first_flg) {
                    $where_sql .= $search_free_ans_sql;
                    $first_flg = false;
                } else {
                    if ($have_free_ans_condition_flg) {
                        $where_sql .= " OR ( " . $search_free_ans_sql . ") ";
                    } else {
                        $where_sql .= " AND ( " . $search_free_ans_sql;
                    }
                }
                $have_free_ans_condition_flg = true;
                continue;
            }

            if (!$switch_value) $switch_value = $condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] ? $condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] : self::QUERY_TYPE_OR;

            if ($switch_value == self::QUERY_TYPE_AND) {
                if ($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                    if ($first_flg) {
                        $where_sql .= " ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                    } else {
                        $where_sql .= " AND ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                    }
                } else {
                    if ($first_flg) {
                        $where_sql .= " ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                    } else {
                        $where_sql .= " AND ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                    }
                }
            } else {
                if ($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                    if ($first_flg) {
                        $where_sql .= " ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                    } else {
                        $where_sql .= " OR ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                    }
                } else {
                    if ($first_flg) {
                        $where_sql .= " ans{$relate_id}.choice_id = {$user_answer} ";
                    } else {
                        $where_sql .= " OR ans{$relate_id}.choice_id = {$user_answer} ";
                    }
                }
            }
            $first_flg = false;
        }

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchDeliveryTimeWhereClause($condition, $action_id) {
        $where_sql = '';

        if (count($condition) == 1 && current($condition) == self::DID_NOT_SEND) {
            $where_sql .= 'target' . $action_id . '.user_id IS NULL ';
            return $where_sql;
        }

        $id_arr = '';
        $not_send = false;
        foreach ($condition as $input_name => $input_value) {
            if ($input_value == self::DID_NOT_SEND) {
                $not_send = true;
                continue;
            }
            $id_arr .= '"' . $input_value . '",';
        }
        $id_arr = trim($id_arr, ',');

        $where_sql .= 'target' . $action_id . '.cp_message_delivery_reservation_id IN (' . $id_arr . ') ';
        if ($not_send) {
            $where_sql .= ' OR target' . $action_id . '.user_id IS NULL ';
        }

        return $where_sql;
    }

    /**
     * 送信済み・送信対象に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchQueryUserWhereClause($condition) {
        $where_sql = "";
        $query_user_condition = explode('/', $condition);

        if ($query_user_condition[0] == self::QUERY_USER_TARGET) {
            $where_sql = "tar.id IS NOT NULL";
        } elseif ($query_user_condition[0] == self::QUERY_USER_SENT) {
            $where_sql = "mes.id IS NOT NULL";
        }

        return $where_sql;
    }

    /**
     * ファン全員を返すか
     * @param $condition
     * @return string
     */
    protected function getJoinUserOnlyWhereClause($condition) {
        $where_sql = "";

        if ($condition) {
            $where_sql = "cp_usr.id IS NOT NULL";
        }

        return $where_sql;
    }

    /**
     * 写真投稿シェアSNS
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchPhotoShareSnsWhereClause($condition, $action_id) {
        $where_sql = '';
        $escape_action_id = $this->escape($action_id);
        $name = 'search_photo_share_sns/' . $action_id . '/';
        $exist_same_action = FALSE;
        $switch_type = $condition['switch_type/' . self::SEARCH_PHOTO_SHARE_SNS . '/' . $action_id];

        if ($switch_type == self::QUERY_TYPE_AND) {
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                $where_sql .= " (user_share{$fb_alias}.execute_status = 1 AND user_share{$fb_alias}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_FACEBOOK . ") ";
                $exist_same_action = TRUE;
            }
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                if ($exist_same_action) {
                    $where_sql .= " AND ";
                }
                $where_sql .= " (user_share{$tw_alias}.execute_status = 1 AND user_share{$tw_alias}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_TWITTER . ") ";
                $exist_same_action = TRUE;
            }
            if ($condition[$name . '-1']) {
                $not_share_alias = $this->escape($action_id) . '_' . '99';//-1はテーブル別名に指定できない
                if ($exist_same_action) {
                    $where_sql .= " AND ";
                }
                $where_sql .= " (user_share{$not_share_alias}.execute_status = 0 OR user_share{$not_share_alias}.id IS NULL) ";
            }
        } else {
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                $where_sql .= " (user_share{$escape_action_id}.execute_status = 1 AND user_share{$escape_action_id}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_FACEBOOK . ") ";
                $exist_same_action = TRUE;
            }
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                if ($exist_same_action) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " (user_share{$escape_action_id}.execute_status = 1 AND user_share{$escape_action_id}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_TWITTER . ") ";
                $exist_same_action = TRUE;
            }
            if ($condition[$name . '-1']) {
                if ($exist_same_action) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " (user_share{$escape_action_id}.execute_status = 0 OR user_share{$escape_action_id}.id IS NULL) ";
            }
        }

        return $where_sql;
    }

    /**
     * 写真投稿シェアテキスト
     * シェアSNSのAND/ORによってSQLを分ける
     * @param $condition 写真投稿シェアテキストの絞り込み条件
     * @param $action_id
     * @param $search_conditions
     * @return string
     */
    protected function getSearchPhotoShareTextWhereClause($condition, $action_id, $search_conditions) {
        $where_sql = '';
        $search_exist = FALSE;
        $escape_action_id = $this->escape($action_id);
        $name = 'search_photo_share_text/' . $action_id . '/';
        $sns_name = 'search_photo_share_sns/' . $action_id . '/';

        if ($share_sns = $search_conditions[self::SEARCH_PHOTO_SHARE_SNS . '/' . $action_id]) {
            $switch_type = $share_sns['switch_type/' . self::SEARCH_PHOTO_SHARE_SNS . '/' . $action_id];

            if ($switch_type == self::QUERY_TYPE_AND) {
                // テキストの有無は下記のいずれか1テーブルを参照できればよい
                if ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                    $share_table = " user_share{$fb_alias} ";
                } elseif ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                    $share_table = " user_share{$tw_alias} ";
                } elseif ($share_sns[$sns_name . '-1']) {
                    $not_share_alias = $this->escape($action_id) . '_' . '99'; // -1はテーブル別名に指定できない
                    $share_table = " user_share{$not_share_alias} ";
                }
            } else {
                $share_table = "user_share{$escape_action_id}";
            }
        } elseif ($condition) {
            $share_table = "user_share{$escape_action_id}";
        }

        if (isset($condition[$name . PhotoUserShare::SEARCH_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_EXISTS] == PhotoUserShare::SEARCH_EXISTS) {
            $where_sql .= " ({$share_table}.execute_status = 1 AND ({$share_table}.share_text IS NOT NULL AND {$share_table}.share_text != '')) ";
            $search_exist = TRUE;
        }
        if (isset($condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS] == PhotoUserShare::SEARCH_NOT_EXISTS) {
            if ($search_exist) {
                $where_sql .= " OR ";
            }
            $where_sql .= " ({$share_table}.share_text IS NULL OR {$share_table}.share_text = '') ";
        }

        return $where_sql;
    }

    /**
     * 写真投稿の承認
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchPhotoApprovalStatusWhereClause($condition, $action_id) {
        $sns = array();
        $escape_action_id = $this->escape($action_id);
        $name = 'search_photo_approval_status/' . $action_id . '/';

        if (isset($condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT]) &&
            $condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] == PhotoUser::APPROVAL_STATUS_DEFAULT
        ) {
            $sns[] = PhotoUser::APPROVAL_STATUS_DEFAULT;
        }
        if (isset($condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE]) &&
            $condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE] == PhotoUser::APPROVAL_STATUS_APPROVE
        ) {
            $sns[] = PhotoUser::APPROVAL_STATUS_APPROVE;
        }
        if (isset($condition[$name . PhotoUser::APPROVAL_STATUS_REJECT]) &&
            $condition[$name . PhotoUser::APPROVAL_STATUS_REJECT] == PhotoUser::APPROVAL_STATUS_REJECT
        ) {
            $sns[] = PhotoUser::APPROVAL_STATUS_REJECT;
        }
        $where_sql = "photo_user{$escape_action_id}.approval_status IN(" . implode(',', $sns) . ")";

        return $where_sql;
    }

    /**
     * シェア状況
     * @param $condition
     * @return string
     */
    protected function getSearchShareUserLogTypeWhereClause($condition) {
        $type = array();

        foreach ($condition as $key => $value) {
            if (preg_match('#^search_share_type/#', $key)) {
                $split_key = explode('/', $key);
                $type[] = $split_key[1];
            }
        }

        return "share_user_logs.type IN(" . implode(',', $type) . ")";
    }

    /**
     * シェアコメント
     * @param $condition
     * @return string
     */
    protected function getSearchShareUserLogTextWhereClause($condition) {
        $where_sql = array();

        foreach ($condition as $key => $value) {
            if (preg_match('#^search_share_text/#', $key)) {
                $split_key = explode('/', $key);
                if ($split_key[1] == CpShareUserLog::SEARCH_EXISTS) {
                    $where_sql[] = "CHAR_LENGTH(share_user_logs.text) > 0";
                } elseif ($split_key[1] == CpShareUserLog::SEARCH_NOT_EXISTS) {
                    $where_sql[] = "(CHAR_LENGTH(share_user_logs.text) = 0 || share_user_logs.text IS NULL)";
                }
            }
        }

        if (count($where_sql) == 1) {
            $where_sql = current($where_sql);
        } else {
            $where_sql = "(" . implode(' OR ', $where_sql) . ")";
        }

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchInstagramHashtagDuplicateWhereClause($condition, $action_id) {
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $name = 'search_instagram_hashtag_duplicate/' . $action_id . '/';

        if (isset($condition[$name . InstagramHashtagUser::SEARCH_EXISTS]) &&
            $condition[$name . InstagramHashtagUser::SEARCH_EXISTS] == InstagramHashtagUser::SEARCH_EXISTS
        ) {
            $status[] = InstagramHashtagUser::SEARCH_EXISTS;
        }
        if (isset($condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS]) &&
            $condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] == InstagramHashtagUser::SEARCH_NOT_EXISTS
        ) {
            $status[] = InstagramHashtagUser::SEARCH_NOT_EXISTS;
        }

        $where_sql = "hashtag_users{$escape_action_id}.duplicate_flg IN(" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchInstagramHashtagReverseWhereClause($condition, $action_id) {
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $name = 'search_instagram_hashtag_reverse/' . $action_id . '/';

        if (isset($condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT]) &&
            $condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] == InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT
        ) {
            $status[] = InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT;
        }
        if (isset($condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID]) &&
            $condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] == InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID
        ) {
            $status[] = InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID;
        }
        $where_sql = "hashtag_user_posts{$escape_action_id}.reverse_post_time_flg IN(" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchInstagramHashtagApprovalStatusWhereClause($condition, $action_id) {
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $name = 'search_instagram_hashtag_approval_status/' . $action_id . '/';

        if (isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT]) &&
            $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] == InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT
        ) {
            $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT;
        }
        if (isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE]) &&
            $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] == InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE
        ) {
            $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE;
        }
        if (isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT]) &&
            $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] == InstagramHashtagUserPost::APPROVAL_STATUS_REJECT
        ) {
            $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
        }
        $where_sql = "hashtag_user_posts{$escape_action_id}.approval_status IN(" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchFbLikeLogStatusWhereClause($condition, $action_id) {
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $prefix = 'search_fb_like_type/' . $action_id . '/';

        foreach ($condition as $key => $value) {
            if (preg_match('#^' . $prefix . '#', $key)) {
                $split_key = explode('/', $key);
                $status[] = $split_key[2];
            }
        }

        $where_sql = "fb_like_logs{$escape_action_id}.status IN (" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchTwFollowLogStatusWhereClause($condition, $action_id) {
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $prefix = 'search_tw_follow_type/' . $action_id . '/';

        foreach ($condition as $key => $value) {
            if (preg_match('#^' . $prefix . '#', $key)) {
                $split_key = explode('/', $key);
                $status[] = $split_key[2];
            }
        }

        $where_sql = "tw_follow_logs{$escape_action_id}.status IN (" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchTweetMessageStatusWhereClause($condition, $action_id) {
        $where_sql = array();
        $status = array();
        $escape_action_id = $this->escape($action_id);
        $prefix = 'search_tweet_type/' . $action_id . '/';

        foreach ($condition as $key => $value) {
            if (preg_match('#^' . $prefix . '#', $key)) {
                $split_key = explode('/', $key);
                $status[] = $split_key[2];
            }
        }
        if (in_array(TweetMessage::TWEET_ACTION_EXEC, $status)) {
            $where_sql[] = "(tweet_messages{$escape_action_id}.skipped = 0 AND tweet_messages{$escape_action_id}.tweet_content_url != '')";
        }
        if (in_array(TweetMessage::TWEET_ACTION_SKIP, $status)) {
            $where_sql[] = "(tweet_messages{$escape_action_id}.skipped = 1)";
        }

        if (count($where_sql) == 1) {
            $where_sql = current($where_sql);
        } else {
            $where_sql = "(" . implode(' OR ', $where_sql) . ")";
        }

        return $where_sql;
    }

    /**
     * チャンネル登録状況
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchYoutubeChannelApprovalStatusWhereClause($condition, $action_id) {
        $status = array();

        $escape_action_id = $this->escape($action_id);
        foreach ($condition as $key => $value) {
            if (preg_match('#^search_ytch_subscription_type/#', $key)) {
                $split_key = explode('/', $key);
                $status[] = $split_key[2];
            }
        }
        $where_sql = "ytch_user_logs{$escape_action_id}.status IN(" . implode(',', $status) . ")";

        return $where_sql;
    }

    /**
     * @param $condition
     * @return string
     */
    protected function getSearchGiftReceiverFanWhereClause($condition) {
        $action_id = current($condition);
        $escape_action_id = $this->escape($action_id);
        $where_sql = " AND gift_message{$escape_action_id}.receiver_user_id > 0";

        return $where_sql;
    }

    /**
     * 人気投票
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchPopularVoteCandidateWhereClause($condition, $action_id) {

        $escape_action_id = $this->escape($action_id);
        $where_sql = "(";
        $name = 'search_popular_vote_candidate/' . $action_id . '/';

        if ($condition[$action_id]) {
            $user_table = "popular_vote_user{$escape_action_id}";
        }

        $search_exist = false;
        foreach ($condition as $key => $value) {
            if ($search_exist) {
                $where_sql .= " OR ";
            }

            if ($key === $name . CpPopularVoteCandidate::SEARCH_NOT_VOTED) {
                $where_sql .= " ({$user_table}.cp_popular_vote_candidate_id IS NULL OR {$user_table}.cp_popular_vote_candidate_id = '' OR {$user_table}.cp_popular_vote_candidate_id = 0) ";
                $search_exist = true;
            } else {
                $where_sql .= " ({$user_table}.cp_popular_vote_candidate_id = {$value}) ";
                $search_exist = true;
            }
        }

        $where_sql .= ")";

        return $where_sql;
    }

    /**
     * 人気投票シェアSNS
     * @param $condition
     * @param $action_id
     * @return string
     */
    protected function getSearchPopularVoteShareSnsWhereClause($condition, $action_id) {
        $where_sql = "(";
        $exist_same_action = false;
        $escape_action_id = $this->escape($action_id);
        $name = 'search_popular_vote_share_sns/' . $action_id . '/';
        $switch_type = $condition['switch_type/' . self::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action_id];

        if ($switch_type == self::QUERY_TYPE_AND) {
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                $where_sql .= " (popular_vote_user_share{$fb_alias}.execute_status = 1 AND popular_vote_user_share{$fb_alias}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_FACEBOOK . ") ";
                $exist_same_action = true;
            }
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                if ($exist_same_action) {
                    $where_sql .= " AND ";
                }
                $where_sql .= " (popular_vote_user_share{$tw_alias}.execute_status = 1 AND popular_vote_user_share{$tw_alias}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_TWITTER . ") ";
                $exist_same_action = true;
            }
            if ($condition[$name . '-1']) {
                $not_share_alias = $this->escape($action_id) . '_' . '99';//-1はテーブル別名に指定できない
                if ($exist_same_action) {
                    $where_sql .= " AND ";
                }
                $where_sql .= " (popular_vote_user_share{$not_share_alias}.execute_status = 0 OR popular_vote_user_share{$not_share_alias}.id IS NULL) ";
            }
        } else {
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 1 AND popular_vote_user_share{$escape_action_id}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_FACEBOOK . ") ";
                $exist_same_action = true;
            }
            if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                if ($exist_same_action) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 1 AND popular_vote_user_share{$escape_action_id}.social_media_type = " . SocialAccount::SOCIAL_MEDIA_TWITTER . ") ";
                $exist_same_action = true;
            }
            if ($condition[$name . '-1']) {
                if ($exist_same_action) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 0 OR popular_vote_user_share{$escape_action_id}.id IS NULL) ";
            }
        }
        $where_sql .= ")";

        return $where_sql;
    }

    /**
     * 人気投票シェアテキスト
     * @param $condition 人気投票シェアテキストの絞り込み条件
     * @param $action_id
     * @param $search_conditions
     * @return string
     */
    protected function getSearchPopularVoteShareTextWhereClause($condition, $action_id, $search_conditions) {
        $where_sql = "(";
        $escape_action_id = $this->escape($action_id);
        $sns_name = 'search_popular_vote_share_sns/' . $action_id . '/';
        $name = 'search_popular_vote_share_text/' . $action_id . '/';

        if ($share_sns = $search_conditions[self::SEARCH_POPULAR_VOTE_SHARE_SNS . "/" . $action_id]) {
            $switch_type = $share_sns['switch_type/' . self::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action_id];
            if ($switch_type == self::QUERY_TYPE_AND) {
                // テキストの有無は下記のいずれか1テーブルを参照できればよい
                if ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                    $share_table = " popular_vote_user_share{$fb_alias} ";
                } elseif ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                    $share_table = " popular_vote_user_share{$tw_alias} ";
                } elseif ($share_sns[$sns_name . '-1']) {
                    $not_share_alias = $this->escape($action_id) . '_' . '99'; // -1はテーブル別名に指定できない
                    $share_table = " popular_vote_user_share{$not_share_alias} ";
                }
            } else {
                $share_table = "popular_vote_user_share{$escape_action_id}";
            }
        } elseif ($condition) {
            $share_table = "popular_vote_user_share{$escape_action_id}";
        }

        if (isset($condition[$name . PhotoUserShare::SEARCH_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_EXISTS] == PhotoUserShare::SEARCH_EXISTS) {
            $where_sql .= " ({$share_table}.execute_status = 1 AND ({$share_table}.share_text IS NOT NULL AND {$share_table}.share_text != '')) ";
            $search_exist = true;
        }
        if (isset($condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS] == PhotoUserShare::SEARCH_NOT_EXISTS) {
            if ($search_exist) {
                $where_sql .= " OR ";
            }
            $where_sql .= " ({$share_table}.share_text IS NULL OR {$share_table}.share_text = '') ";
        }
        $where_sql .= " ) ";

        return $where_sql;
    }

    /**
     * snsインタラクティブ
     * @param $condition
     * @return string
     */
    protected function getSearchSocialAccountInteractiveWhereClause($condition) {
        $where_sql = '';
        unset($condition['not_flg']);

        // No need where clause if there are more or less than 1 interactive condition
        if (count($condition) != 1) {
            return $where_sql;
        }

        // Get current key of interactive condition (value is useless in this case)
        $interactive_condition = key($condition);

        if (!preg_match('/^search_social_account_interactive\//', $interactive_condition)) {
            return $where_sql;
        }

        $split_key = explode('/', $interactive_condition);
        $social_app_id = $split_key[1];
        $social_media_id = $split_key[2];
        $status = $split_key[3];

        $alias = $social_app_id . '_' . $social_media_id;

        if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            if ($status == self::LIKED) {
                $where_sql .= "SA_{$social_app_id}.user_id IS NOT NULL AND sns_like_{$alias}.like_id IS NOT NULL";
            } else {
                $where_sql .= "SA_{$social_app_id}.user_id IS NULL OR sns_like_{$alias}.like_id IS NULL";
            }
        } elseif ($social_app_id == SocialApps::PROVIDER_TWITTER) {
            if($status == self::FOLLOWED) {
                $where_sql .= "SA_{$social_app_id}.user_id IS NOT NULL  AND TL_{$alias}.follower_id IS NOT NULL " ;
            } else {
                $where_sql .= "SA_{$social_app_id}.user_id IS NULL OR TL_{$alias}.follower_id IS NULL" ;
            }
        }

        return $where_sql;
    }

    /**
     * @param $condition
     * @return string
     */
    protected function getSearchDuplicateAddressWhereClause($condition) {
        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::NOT_HAVE_ADDRESS] || $condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::HAVE_ADDRESS]) {
            return $this->getSearchDuplicateAddressBrandUserWhereClause($condition);
        }
        return $this->getSearchDuplicateAddressCpUserWhereClause($condition);
    }

    /**
     * ブランドユーザ重複住所絞り込み
     * @param $condition
     * @return string
     *
     */
    protected function getSearchDuplicateAddressBrandUserWhereClause($condition) {
        $where_sql = '(';

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::NOT_HAVE_ADDRESS]) {
            $where_sql .= ' relate.duplicate_address_count = ' . BrandsUsersRelationService::NOT_HAVE_ADDRESS . ' OR relate.duplicate_address_count IS NULL  ';
        }

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::NOT_HAVE_ADDRESS] && $condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::HAVE_ADDRESS]) {
            $where_sql .= ' OR ';
        }

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_DUPLICATE . '/' . self::HAVE_ADDRESS]) {

            if ($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' relate.duplicate_address_count > 0 ';
            } elseif ($condition['search_duplicate_address_from'] !== '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' relate.duplicate_address_count >= ' . $condition['search_duplicate_address_from'];
            } elseif ($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] !== '') {
                $where_sql .= ' (relate.duplicate_address_count > 0 AND relate.duplicate_address_count <=' . $condition['search_duplicate_address_to'] . ') ';
            } else {
                $where_sql .= ' (relate.duplicate_address_count >=' . $condition['search_duplicate_address_from'] . ' AND relate.duplicate_address_count <=' . $condition['search_duplicate_address_to'] . ') ';
            }

        }

        $where_sql .= ' ) ';

        return $where_sql;
    }

    /**
     * cpユーザ重複住所絞り込み
     * @param $condition
     * @return string
     *
     */
    protected function getSearchDuplicateAddressCpUserWhereClause($condition) {

        $where_sql = '(';

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_USER_DUPLICATE . '/' . self::NOT_HAVE_ADDRESS]) {
            $where_sql .= ' cp_usr.duplicate_address_count = ' . CpUser::NOT_HAVE_ADDRESS . ' OR cp_usr.duplicate_address_count IS NULL  ';
        }

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_USER_DUPLICATE . '/' . self::NOT_HAVE_ADDRESS] && $condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_USER_DUPLICATE . '/' . self::HAVE_ADDRESS]) {
            $where_sql .= ' OR ';
        }

        if ($condition['search_duplicate_address/' . self::SHIPPING_ADDRESS_USER_DUPLICATE . '/' . self::HAVE_ADDRESS]) {

            if ($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' cp_usr.duplicate_address_count > 0 ';
            } elseif ($condition['search_duplicate_address_from'] !== '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' cp_usr.duplicate_address_count >= ' . $condition['search_duplicate_address_from'];
            } elseif ($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] !== '') {
                $where_sql .= ' (cp_usr.duplicate_address_count > 0 AND cp_usr.duplicate_address_count <=' . $condition['search_duplicate_address_to'] . ') ';
            } else {
                $where_sql .= ' (cp_usr.duplicate_address_count >=' . $condition['search_duplicate_address_from'] . ' AND cp_usr.duplicate_address_count <=' . $condition['search_duplicate_address_to'] . ') ';
            }

        }

        $where_sql .= ' ) ';

        return $where_sql;
    }

    public function getConditionsText($search_conditions) {
        $conditions_array = array();
        $conditions_text_array = $this->getConditionsData($search_conditions);

        foreach ($conditions_text_array as $key => $value) {
            if (preg_match('/^segmenting_condition_/', $key)) {
                $content = array();

                foreach ($value as $sub_value) {
                    $content[] = $this->getConditionText($sub_value);
                }

                if($content) {
                    $conditions_array[] = implode(' OR ', $content);
                }

            }
        }

        return implode('/', $conditions_array);
    }

    public function getConditionText($condition) {

        $isNotFlg = Util::isNullOrEmpty($condition['not_flg']) ? false : true;

        $conditionText = $isNotFlg ? 'NOT' : '';
        
        $conditionText .= '(' . $this->getShortenConditionText($condition);

        $conditionText .= ')';
        return $conditionText;
    }

    public function getShortenConditionText($condition) {

        $conditionText = '';
        $first_flg = true;
        unset($condition['not_flg']);
        foreach($condition as $key_data => $data) {

            if($first_flg) {
                $conditionText .= $data['title'].': ';

                if(strpos($key_data, 'search_social_account/') !== false) {
                    $conditionText .= '('. $data['content'];
                } else {
                    $conditionText .= $data['content'];
                }
            } else {
                if(strpos($key_data, 'search_friend_count/') !== false) {
                    $conditionText .= ') AND ' . $data['content'];
                } else {
                    $conditionText .= ' | ' . $data['content'];
                }
            }

            $first_flg = false;
        }

        if(strpos($key_data, 'search_friend_count/') !== false || strpos($key_data, 'search_social_account/') !== false) {
            $conditionText .= ')';
        }

        return $conditionText;
    }

    /**
     * @param $search_conditions
     * @return array
     */
    public function getConditionsData($search_conditions) {
        $conditions_text = array();

        foreach ($search_conditions as $key => $value) {
            if (preg_match('/^segmenting_condition_/', $key)) {
                $temp_condition[$key] = $this->getConditionsData($value);

                if (!is_array($temp_condition[$key])) continue;
            } else {
                $temp_condition = $this->getConditionData($key, $value);
            }

            if (!is_array($temp_condition)) continue;

            if (preg_match('/^segmenting_condition_/', $key)) {
                $conditions_text = array_merge($conditions_text, $temp_condition);
            } else {
                $temp_condition['not_flg'] = $value['not_flg'];
                $conditions_text[$key] = $temp_condition;
            }
        }

        return $conditions_text;
    }

    /**
     * @param $search_key
     * @param $search_condition
     * @return array|null
     */
    public function getConditionData($search_key, $search_condition) {
        $condition_text = null;
        list($search_type, $search_sub_key) = $this->parseSearchKey($search_key);

        switch ($search_type) {
            case self::SEARCH_PROFILE_RATE:
                $condition_text = $this->getProfileRateSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_MEMBER_NO:
                $condition_text = $this->getProfileMemberNoSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_REGISTER_PERIOD:
            case self::SEARCH_PROFILE_LAST_LOGIN:
            case self::SEARCH_PROFILE_LOGIN_COUNT:
            case self::SEARCH_CP_ENTRY_COUNT:
            case self::SEARCH_CP_ANNOUNCE_COUNT:
            case self::SEARCH_MESSAGE_DELIVERED_COUNT:
            case self::SEARCH_MESSAGE_READ_COUNT:
            case self::SEARCH_MESSAGE_READ_RATIO:
            case self::SEARCH_SOCIAL_ACCOUNT_SUM:
                $condition_text = $this->getRangeSearchConditionData($search_condition, $search_type);
                break;
            case self::SEARCH_PROFILE_SOCIAL_ACCOUNT:
                $condition_text = $this->getProfileSocialAccountSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PROFILE_SEX:
                $condition_text = $this->getProfileSexSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_AGE:
                $condition_text = $this->getProfileAgeSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_ADDRESS:
                $condition_text = $this->getProfileAddressSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS:
                $condition_text = $this->getProfileQuestionnaireStatusSearchConditionData($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE:
            case self::SEARCH_QUESTIONNAIRE:
                $condition_text = $this->getQuestionnaireSearchConditionData($search_condition, $search_type);
                break;
            case self::SEARCH_PROFILE_CONVERSION:
                $condition_text = $this->getProfileConversionSearchConditionData($search_condition);
                break;
            case self::SEARCH_PARTICIPATE_CONDITION:
                $condition_text = $this->getParticipateConditionSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_SHARE_SNS:
                $condition_text = $this->getPhotoShareSnsSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_SHARE_TEXT:
                $condition_text = $this->getPhotoShareTextSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_APPROVAL_STATUS:
                $condition_text = $this->getPhotoApprovalStatusSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SHARE_TYPE:
                $condition_text = $this->getShareTypeSearchConditionData($search_condition);
                break;
            case self::SEARCH_SHARE_TEXT:
                $condition_text = $this->getShareTextSearchConditionData($search_condition);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION:
                $condition_text = $this->getInstagramHashtagDuplicationSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME:
                $condition_text = $this->getInstagramHashtagReversePostTimeSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS:
                $condition_text = $this->getInstagramHashtagApprovalStatusSearchConditionKey($search_condition, $search_sub_key);
                break;
            case self::SEARCH_FB_LIKE_TYPE:
                $condition_text = $this->getFbLikeTypeSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_TW_FOLLOW_TYPE:
                $condition_text = $this->getTwFollowTypeSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION:
                $condition_text = $this->getYoutubeChannelSubscriptionSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_CANDIDATE:
                $condition_text = $this->getPopularVoteCandidateSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_SNS:
                $condition_text = $this->getPopularVoteShareSnsSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_TEXT:
                $condition_text = $this->getPopularVoteShareTextSearchConditionData($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE:
                $condition_text = $this->getSocialAccountInteractiveSearchConditionData($search_condition, $search_key);
                break;
            case self::SEARCH_IMPORT_VALUE:
                $condition_text = $this->getImportValueSearchConditionData($search_condition, $search_sub_key);
                break;
            default:
                // TODO 対応しない
                break;
        }

        return $condition_text;
    }

    /**
     * @param $search_key
     * @param null $brand_id
     * @return array|mixed|string
     */
    public function getConditionTitle($search_key, $brand_id = null) {
        list($search_type, $search_sub_key) = $this->parseSearchKey($search_key);

        switch ($search_type) {
            case self::SEARCH_PROFILE_RATE:
            case self::SEARCH_PROFILE_MEMBER_NO:
            case self::SEARCH_PROFILE_REGISTER_PERIOD:
            case self::SEARCH_PROFILE_LAST_LOGIN:
            case self::SEARCH_PROFILE_LOGIN_COUNT:
            case self::SEARCH_PROFILE_SEX:
            case self::SEARCH_PROFILE_AGE:
            case self::SEARCH_PROFILE_ADDRESS:
            case self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS:
            case self::SEARCH_PHOTO_SHARE_SNS:
            case self::SEARCH_PHOTO_SHARE_TEXT:
            case self::SEARCH_PHOTO_APPROVAL_STATUS:
            case self::SEARCH_SHARE_TYPE:
            case self::SEARCH_SHARE_TEXT:
            case self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION:
            case self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME:
            case self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS:
            case self::SEARCH_FB_LIKE_TYPE:
            case self::SEARCH_TW_FOLLOW_TYPE:
            case self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION:
            case self::SEARCH_POPULAR_VOTE_CANDIDATE:
            case self::SEARCH_POPULAR_VOTE_SHARE_SNS:
            case self::SEARCH_POPULAR_VOTE_SHARE_TEXT:
                $condition_text = self::$segment_provision_condition_label[$search_type];
                break;
            case self::SEARCH_CP_ENTRY_COUNT:
            case self::SEARCH_CP_ANNOUNCE_COUNT:
            case self::SEARCH_MESSAGE_DELIVERED_COUNT:
            case self::SEARCH_MESSAGE_READ_COUNT:
            case self::SEARCH_MESSAGE_READ_RATIO:
            case self::SEARCH_SOCIAL_ACCOUNT_SUM:
                $condition_text = $this->getRangeSearchConditionData(array(), $search_type);
                break;
            case self::SEARCH_PROFILE_SOCIAL_ACCOUNT:
                $condition_text = SocialAccount::$socialMediaTypeName[$search_sub_key];
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE:
            case self::SEARCH_QUESTIONNAIRE:
                $condition_text = $this->getQuestionnaireSearchConditionTitle($search_key);
                break;
            case self::SEARCH_PROFILE_CONVERSION:
                $condition_text = $this->getProfileConversionSearchConditionTitle($search_sub_key);
                break;
            case self::SEARCH_PARTICIPATE_CONDITION:
                $condition_text = $this->getParticipateConditionSearchConditionTitle($search_sub_key);
                break;
            case self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE:
                $condition_text = $this->getSocialAccountInteractiveSearchConditionTitle($search_key, $brand_id);
                break;
            case self::SEARCH_IMPORT_VALUE:
                $condition_text = $this->getImportValueSearchConditionTitle($search_key);
                break;
            default:
                // TODO 対応しない
                break;
        }

        return $condition_text;
    }

    /**
     * @param $search_key
     * @return string
     */
    public function getQuestionnaireSearchConditionTitle($search_key) {
        $split_key = explode('/', $search_key);
        $search_type = $split_key[0];

        if ($search_type == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_CP_QUESTION);
            $relate_id = $split_key[2];
            $title = 'アンケート';
        } else {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            $relate_id = $split_key[1];
            $title = 'カスタムプロフィール';
        }

        $profile_question_relate = $questionnaire_service->getProfileQuestionRelationsById($relate_id);
        $question = $questionnaire_service->getQuestionById($profile_question_relate->question_id);

        $title .= "/Q" . $profile_question_relate->number . " " . $question->question;
        return $title;
    }

    /**
     * @param $conversion_id
     * @return string
     */
    public function getProfileConversionSearchConditionTitle($conversion_id) {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->getService("ConversionService");
        $conversion = $conversion_service->getConversionById($conversion_id);
        $title = 'コンバージョン' . ' ' . $conversion->name;

        return $title;
    }

    /**
     * @param $action_id
     * @return mixed
     */
    public function getParticipateConditionSearchConditionTitle($action_id) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        $cp_action = $cp_flow_service->getCpActionById($action_id);
        $action_detail = $cp_action->getCpActionDetail();

        return $action_detail['title'];
    }

    /**
     * @param $search_key
     * @param $brand_id
     * @return string
     */
    public function getSocialAccountInteractiveSearchConditionTitle($search_key, $brand_id) {
        /** @var  BrandSocialAccountService $brandSocialAccountService */
        $brandSocialAccountService = $this->getService('BrandSocialAccountService');

        $split_key = explode('/', $search_key);
        $social_app_id = $split_key[1];
        $page_id = $split_key[2];
        $page = $brandSocialAccountService->getBrandSocialAccount($brand_id, $page_id, $social_app_id);

        return Util::cutTextByWidth($page->name, 150);
    }

    /**
     * @param $search_key
     * @return string
     */
    public function getImportValueSearchConditionTitle($search_key) {
        $brand_service = $this->getService('BrandService');

        $split_key = explode('/', $search_key);
        $definition_id = $split_key[1];

        $definition = $brand_service->getBrandUserAttributeDefinitionById($definition_id);

        return Util::cutTextByWidth($definition->attribute_name, 150);
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileRateSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = '評価';

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_rate\//', $key)) {
                $data['content'] = explode('/', $key)[1];
                if ($data['content'] == BrandsUsersRelationService::BLOCK) {
                    $data['content'] = 'ブロックユーザー';
                } elseif ($data['content'] == BrandsUsersRelationService::NON_RATE) {
                    $data['content'] = '未評価';
                }
                $result[$key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return string
     */
    public function getProfileMemberNoSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = '会員No';

        if (isset($search_condition['search_profile_member_no_from'])) {
            $data['content'] = $search_condition['search_profile_member_no_from'];
            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $media_type
     * @return array
     */
    public function getProfileSocialAccountSearchConditionData($search_condition, $media_type) {
        $result = array();
        $data['title'] = SocialAccount::$socialMediaTypeName[$media_type];

        if (isset($search_condition['search_social_account/' . $media_type . '/' . CpCreateSqlService::LINK_SNS])) {
            $data['content'] = '連携';
            $result['search_social_account/' . $media_type . '/' . CpCreateSqlService::LINK_SNS] = $data;
        }

        if (isset($search_condition['search_social_account/' . $media_type . '/' . CpCreateSqlService::NOT_LINK_SNS])) {
            $data['content'] = '未連携';
            $result['search_social_account/' . $media_type . '/' . CpCreateSqlService::NOT_LINK_SNS] = $data;
        }

        if ((isset($search_condition['search_friend_count_from/' . $media_type]) && $search_condition['search_friend_count_from/' . $media_type] !== '')
            || (isset($search_condition['search_friend_count_to/' . $media_type]) && $search_condition['search_friend_count_to/' . $media_type] !== '')
        ) {
            $data['content'] = '友達数：' . $this->getSearchRangeConditionDataByKey($search_condition, 'search_friend_count', '/' . $media_type);
            $result['search_friend_count/' . $media_type] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getSocialAccountSumSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = '友達数・フォロワー数';

        if (isset($search_condition['search_friend_count_sum_from']) || isset($search_condition['search_friend_count_sum_to'])) {
            $data['content'] = $this->getSearchRangeConditionDataByKey($search_condition, 'search_friend_count_sum');
            $result['search_friend_count_sum'] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileSexSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = '性別';

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_profile_sex\//', $key)) {
                $sex = explode('/', $key)[1];
                if ($sex == UserAttributeService::ATTRIBUTE_SEX_MAN) {
                    $data['content'] = '男';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_MAN] = $data;
                } else if ($sex == UserAttributeService::ATTRIBUTE_SEX_WOMAN) {
                    $data['content'] = '女';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_WOMAN] = $data;
                } else {
                    $data['content'] = '未設定';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_UNKWOWN] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileAgeSearchConditionData($search_condition) {
        $result = array();
        $key = CpCreateSqlService::$search_range_keys[CpCreateSqlService::SEARCH_PROFILE_AGE];
        $data['title'] = CpCreateSqlService::$search_range_labels[CpCreateSqlService::SEARCH_PROFILE_AGE];

        $data['content'] = $this->getSearchRangeConditionDataByKey($search_condition, $key);
        $result[$key] = $data;

        if (isset($search_condition['search_profile_age_not_set'])) {
            $data['content'] = '未設定';
            $result['search_profile_age_not_set'] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileAddressSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = '都道府県';

        /** @var PrefectureService $prefecture_service */
        $prefecture_service = $this->getService('PrefectureService');
        foreach ($search_condition as $key => $address_condition) {
            $prefecture_id = explode('/', $key)[1];
            if (preg_match('/^search_profile_address\//', $key)) {
                if ($prefecture_id == CpCreateSqlService::NOT_SET_PREFECTURE) {
                    $data['content'] = '未設定';
                } else {
                    $prefecture = $prefecture_service->getPrefectureByPrefId($prefecture_id);
                    $data['content'] = $prefecture;
                }
                $result[$key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileQuestionnaireStatusSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = 'カスタムプロフィール';

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_questionnaire_status\//', $key)) {
                $status = explode('/', $key)[1];
                if ($status == BrandsUsersRelation::SIGNUP_WITHOUT_INFO) {
                    $data['content'] = '未取得';
                } else if ($status == BrandsUsersRelation::SIGNUP_WITH_INFO) {
                    $data['content'] = '取得済み';
                } else if ($status == BrandsUsersRelation::FORCE_WITH_INFO) {
                    $data['content'] = '要再取得';
                } else {
                    continue;
                }

                $result[$key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $search_type
     * @return array
     */
    public function getQuestionnaireSearchConditionData($search_condition, $search_type) {
        if ($search_type == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_CP_QUESTION);
            $data['title'] = 'アンケート';
            $input_key = "search_questionnaire";
        } else {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            $data['title'] = 'カスタムプロフィール';
            $input_key = "search_profile_questionnaire";
        }
        $result = array();

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^' . $input_key . '\//', $key)) {
                $split_key = explode('/', $key);
                $relate_id = $split_key[1];
                $user_answer = $split_key[2];
                $profile_question_relate = $questionnaire_service->getProfileQuestionRelationsById($relate_id);

                // TODO show question text
                if ($search_condition['questionnaire_type/' . $relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                    if ($user_answer == CpCreateSqlService::ANSWERED_QUESTIONNAIRE) {
                        $data['content'] = "Q" . $profile_question_relate->number . ' 回答済 ';
                    } else if ($user_answer == CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE) {
                        $data['content'] = "Q" . $profile_question_relate->number . ' 未回答 ';
                    }
                } else {
                    if ($user_answer == CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE) {
                        $data['content'] = "Q" . $profile_question_relate->number . ' 未回答 ';
                    } else {
                        $question_choice_answer = $questionnaire_service->getChoiceById($user_answer);
                        $data['content'] = "Q" . $profile_question_relate->number . '/A' . $question_choice_answer->choice_num;
                    }
                }

                $result[$key] = $data;
            }
        }

        unset ($this->_Services["CpQuestionnaireService"]);
        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileConversionSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = "コンバージョン";

        /** @var ConversionService $conversion_service */
        $conversion_service = $this->getService("ConversionService");
        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_profile_conversion_/', $key)) {
                if ($conversion_id = explode('/', $key)[1]) {
                    $conversion = $conversion_service->getConversionById($conversion_id);
                    $data['title'] .= '/' . $conversion->name;
                    $data['content'] = $this->getSearchRangeConditionDataByKey($search_condition, 'search_profile_conversion', '/' . $conversion_id);
                    $result[$key] = $data;
                    break;
                }
            }
        }

        return $result;
    }

    public function getParticipateConditionSearchConditionData($search_condition, $action_id) {
        $result = array();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        $cp_action = $cp_flow_service->getCpActionById($action_id);
        $action_detail = $cp_action->getCpActionDetail();
        $data['title'] = $action_detail['title'];

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_participate_condition\//', $key)) {
                $participate_status = explode('/', $key)[2];
                switch ($participate_status) {
                    case CpCreateSqlService::PARTICIPATE_COMPLETE:
                        $status = "完了";
                        break;
                    case CpCreateSqlService::PARTICIPATE_REJECTED:
                        $status = "参加条件外";
                        break;
                    case CpCreateSqlService::PARTICIPATE_READ:
                        $status = "既読";
                        break;
                    case CpCreateSqlService::PARTICIPATE_NOT_READ:
                        $status = "未読";
                        break;
                    case CpCreateSqlService::PARTICIPATE_NOT_SEND:
                        $status = "未送信";
                        break;
                    case CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN:
                        $status = "抽選回数指定 ";
                        if ($search_condition['search_count_instant_win_from/' . $action_id]) {
                            $status .= $search_condition['search_count_instant_win_from/' . $action_id];
                        }
                        $status .= '〜';
                        if ($search_condition['search_count_instant_win_to/' . $action_id]) {
                            $status .= $search_condition['search_count_instant_win_to/' . $action_id];
                        }
                        break;
                }
                $data['content'] = $status;
                $result[$key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoShareSnsSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = '写真投稿 シェアSNS';
        $name = 'search_photo_share_sns/' . $action_id . '/';

        if ($search_condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
            $data['content'] = 'Facebook';
            $result[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK] = $data;
        }
        if ($search_condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
            $data['content'] = 'Twitter';
            $result[$name . SocialAccount::SOCIAL_MEDIA_TWITTER] = $data;
        }
        if ($search_condition[$name . '-1']) {
            $data['content'] = '未シェア';
            $result[$name . '-1'] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoShareTextSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = '写真投稿 シェアテキスト';

        $name = 'search_photo_share_text/' . $action_id . '/';
        if (isset($search_condition[$name . PhotoUserShare::SEARCH_EXISTS])) {
            $data['content'] = 'あり';
            $result[$name . PhotoUserShare::SEARCH_EXISTS] = $data;
        }
        if (isset($search_condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS])) {
            $data['content'] = 'なし';
            $result[$name . PhotoUserShare::SEARCH_NOT_EXISTS] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoApprovalStatusSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = '写真投稿 検閲';
        $name = 'search_photo_approval_status/' . $action_id . '/';

        if (isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] == PhotoUser::APPROVAL_STATUS_DEFAULT
        ) {
            $data['content'] = '未承認';
            $result[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] = $data;
        }
        if (isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE] == PhotoUser::APPROVAL_STATUS_APPROVE
        ) {
            $data['content'] = '承認';
            $result[$name . PhotoUser::APPROVAL_STATUS_APPROVE] = $data;
        }
        if (isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_REJECT]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_REJECT] == PhotoUser::APPROVAL_STATUS_REJECT
        ) {
            $data['content'] = '非承認';
            $result[$name . PhotoUser::APPROVAL_STATUS_REJECT] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getShareTypeSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = 'シェア状況';
        $name = 'search_share_type/';

        foreach ($search_condition as $key => $value) {
            if (isset($search_condition[$name . CpShareUserLog::TYPE_SHARE])) {
                $data['content'] = CpShareUserLog::STATUS_SHARE;
                $result[$name . CpShareUserLog::TYPE_SHARE] = $data;
            }
            if (isset($search_condition[$name . CpShareUserLog::TYPE_SKIP])) {
                $data['content'] = CpShareUserLog::STATUS_SKIP;
                $result[$name . CpShareUserLog::TYPE_SKIP] = $data;
            }
            if (isset($search_condition[$name . CpShareUserLog::TYPE_UNREAD])) {
                $data['content'] = CpShareUserLog::STATUS_UNREAD;
                $result[$name . CpShareUserLog::TYPE_UNREAD] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getShareTextSearchConditionData($search_condition) {
        $result = array();
        $data['title'] = 'シェアコメント';
        $name = 'search_share_text/';

        foreach ($search_condition as $key => $value) {
            if (isset($search_condition[$name . CpShareUserLog::SEARCH_EXISTS])) {
                $data['content'] = 'あり';
                $result[$name . CpShareUserLog::SEARCH_EXISTS] = $data;
            }
            if (isset($search_condition[$name . CpShareUserLog::SEARCH_NOT_EXISTS])) {
                $data['content'] = 'なし';
                $result[$name . CpShareUserLog::SEARCH_NOT_EXISTS] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagDuplicationSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'Instagram投稿 ユーザネーム重複';
        $name = 'search_instagram_hashtag_duplicate/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUser::SEARCH_EXISTS]) &&
            $search_condition[$name . InstagramHashtagUser::SEARCH_EXISTS] == InstagramHashtagUser::SEARCH_EXISTS
        ) {
            $data['content'] = 'あり';
            $result[$name . InstagramHashtagUser::SEARCH_EXISTS] = $data;
        }
        if (isset($search_condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS]) &&
            $search_condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] == InstagramHashtagUser::SEARCH_NOT_EXISTS
        ) {
            $data['content'] = 'なし';
            $result[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagReversePostTimeSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'Instagram投稿 登録投稿順序';
        $name = 'search_instagram_hashtag_reverse/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT]) &&
            $search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] == InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT
        ) {
            $data['content'] = '登録後投稿';
            $result[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] = $data;
        }
        if (isset($search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID]) &&
            $search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] == InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID
        ) {
            $data['content'] = '投稿後登録';
            $result[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagApprovalStatusSearchConditionKey($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'Instagram投稿 検閲';
        $name = 'search_instagram_hashtag_approval_status/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] == InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT
        ) {
            $data['content'] = '未承認';
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] = $data;
        }
        if (isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] == InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE
        ) {
            $data['content'] = '承認';
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] = $data;
        }
        if (isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] == InstagramHashtagUserPost::APPROVAL_STATUS_REJECT
        ) {
            $data['content'] = '非承認';
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getFbLikeTypeSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'Facebookいいね！状況';
        $name = 'search_fb_like_type/' . $action_id . '/';

        foreach (CpFacebookLikeLog::$fb_like_statuses as $like_action => $status_action) {
            if (isset($search_condition[$name . $like_action])) {
                $data['content'] = $status_action;
                $result[$name . $like_action] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getTwFollowTypeSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'Twitterフォロー状況';
        $name = 'search_tw_follow_type/' . $action_id . '/';

        foreach (CpTwitterFollowLog::$tw_follow_statuses as $key => $label) {
            if (isset($search_condition[$name . $key])) {
                $data['content'] = $label;
                $result[$name . $key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getYoutubeChannelSubscriptionSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = 'YouTubeチャンネル登録 登録状況';
        $name = 'search_ytch_subscription_type/' . $action_id . '/';

        foreach (CpYoutubeChannelUserLog::$youtube_status_string as $key => $label) {
            if (isset($search_condition[$name . $key])) {
                $data['content'] = $label;
                $search_condition[$name . $key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteCandidateSearchConditionData($search_condition, $action_id) {
        $data['title'] = '人気投票 投票';
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');

        $cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($action_id);
        $cp_popular_vote_candidates = $cp_popular_vote_action->getCpPopularVoteCandidates(array('del_flg' => 0));

        $result = array();
        $name = 'search_popular_vote_candidate/' . $action_id . '/';

        foreach ($cp_popular_vote_candidates as $cp_popular_vote_candidate) {
            if (isset($search_condition[$name . $cp_popular_vote_candidate->id])) {
                $data['content'] = $cp_popular_vote_candidate->title;
                $result[$name . $cp_popular_vote_candidate->id] = $data;
            }
        }

        if (isset($search_condition[$name . CpPopularVoteCandidate::SEARCH_NOT_VOTED])) {
            $data['content'] = '未投票';
            $result[$name . CpPopularVoteCandidate::SEARCH_NOT_VOTED] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteShareSnsSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = '人気投票 シェアSNS';
        $name = 'search_popular_vote_share_sns/' . $action_id . '/';

        if (isset($search_condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK])) {
            $data['content'] = 'Facebook';
            $result[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK] = $data;
        }
        if (isset($search_condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER])) {
            $data['content'] = 'Twitter';
            $result[$name . SocialAccount::SOCIAL_MEDIA_TWITTER] = $data;
        }
        if (isset($search_condition[$name . '-1'])) {
            $data['content'] = '未シェア';
            $result[$name . '-1'] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteShareTextSearchConditionData($search_condition, $action_id) {
        $result = array();
        $data['title'] = '人気投票 シェアされた投票理由';
        $name = 'search_popular_vote_share_text/' . $action_id . '/';

        if (isset($search_condition[$name . PopularVoteUserShare::SEARCH_EXISTS])) {
            $data['title'] = 'あり';
            $result[$name . PopularVoteUserShare::SEARCH_EXISTS] = $data;
        }
        if (isset($search_condition[$name . PopularVoteUserShare::SEARCH_NOT_EXISTS])) {
            $data['title'] = 'なし';
            $result[$name . PopularVoteUserShare::SEARCH_NOT_EXISTS] = $data;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $search_key
     * @return array
     */
    public function getSocialAccountInteractiveSearchConditionData($search_condition, $search_key) {
        /** @var  BrandSocialAccountService $brandSocialAccountService */
        $brandSocialAccountService = $this->getService('BrandSocialAccountService');
        $result = array();

        $split_key = explode('/', $search_key);
        $social_app_id = $split_key[1];
        $page_id = $split_key[2];
        $page = $brandSocialAccountService->getBrandSocialAccountByAccountId($page_id, $social_app_id);
        $data['title'] = Util::cutTextByWidth($page->name, 150);

        foreach ($search_condition as $key => $value) {
            $split_key = explode('/', $key);
            $condition = $split_key[3];
            if ($split_key[0] == 'search_social_account_interactive') {
                if ($condition == 'Y') {
                    $data['content'] = SocialApps::$social_media_page_fan_status[$social_app_id];
                    $result[$key] = $data;
                }
                if ($condition == 'N') {
                    $data['content'] = SocialApps::$social_media_page_not_fan_status[$social_app_id];
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_social_account_is_retweeted_count') {
                if ($condition == 'Y') {
                    $data['content'] = 'リツイート有';
                    $result[$key] = $data;
                }
                if ($condition == 'N') {
                    $data['content'] = 'リツイート無';
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_social_account_is_liked_count') {
                if ($condition == 'Y') {
                    $data['content'] = '投稿にいいね！有';
                    $result[$key] = $data;
                }
                if ($condition == 'N') {
                    $data['content'] = '投稿にいいね！無';
                    $result[$key] = $data;
                }

            } elseif ($split_key[0] == 'search_social_account_is_replied_count') {
                if ($condition == 'Y') {
                    $data['content'] = 'リプライ有';
                    $result[$key] = $data;
                }
                if ($condition == 'N') {
                    $data['content'] = 'リプライ無';
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_social_account_is_commented_count') {
                if ($condition == 'Y') {
                    $data['content'] = '投稿にコメント有';
                    $result[$key] = $data;
                }
                if ($condition == 'N') {
                    $data['content'] = '投稿にコメント無';
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_tw_tweet_retweet_count' && $value) {
                 if ($condition == 'from') {
                    $data['content'] = 'リツイート数 > ' . $value;
                    $result[$key] = $data;
                }
                if ($condition == 'to') {
                    $data['content'] = 'リツイート数 < ' . $value;
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_fb_posts_like_count' && $value) {
                if ($condition == 'from') {
                    $data['content'] = 'いいね数 > ' . $value;
                    $result[$key] = $data;
                }
                if ($condition == 'to') {
                    $data['content'] = 'いいね数 < ' . $value;
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_tw_tweet_reply_count' && $value) {
                if ($condition == 'from') {
                    $data['content'] = 'リプライ数 > ' . $value;
                    $result[$key] = $data;
                }
                if ($condition == 'to') {
                    $data['content'] = 'リプライ数 < ' . $value;
                    $result[$key] = $data;
                }
            } elseif ($split_key[0] == 'search_fb_posts_comment_count' && $value) {
                if ($condition == 'from') {
                    $data['content'] = 'コメント数 > ' . $value;
                    $result[$key] = $data;
                }
                if ($condition == 'to') {
                    $data['content'] = 'コメント数 < ' . $value;
                    $result[$key] = $data;
                }
            } 

        }

        return $result;
    }

    public function getImportValueSearchConditionData($search_condition, $definition_id) {
        $result = array();
        $brand_service = $this->getService('BrandService');
        $bua_definition = $brand_service->getBrandUserAttributeDefinitionById($definition_id);

        $definition_value_set = json_decode($bua_definition->value_set, true);
        $definition_value_set[self::NOT_SET_VALUE] = '未設定';
        $data['title'] = $bua_definition->attribute_name;

        foreach ($search_condition as $key => $value) {
            if (strpos($key, 'search_import_value') !== false) {
                $definition_value = explode('/', $key)[2];
                $data['content'] = $definition_value_set[$definition_value];
                $result[$key] = $data;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $key
     * @param string $extend_key
     * @return string
     */
    protected function getSearchRangeConditionDataByKey($search_condition, $key, $extend_key = "") {
        $text = "";

        if (isset($search_condition[$key . '_from' . $extend_key])) {
            $text .= $search_condition[$key . '_from' . $extend_key];
        }
        $text .= '〜';
        if (isset($search_condition[$key . '_to' . $extend_key])) {
            $text .= $search_condition[$key . '_to' . $extend_key];
        }

        return $text;
    }

    /**
     * @param $search_condition
     * @param $search_type
     * @return array
     */
    public function getRangeSearchConditionData($search_condition, $search_type) {
        $result = array();
        $key = CpCreateSqlService::$search_range_keys[$search_type];
        $data['title'] = CpCreateSqlService::$search_range_labels[$search_type];

        $data['content'] = $this->getSearchRangeConditionDataByKey($search_condition, $key);
        $result[$key] = $data;

        return $result;
    }

    /**
     * @param $search_condition_type
     * @return array
     */
    public static function getSearchConditionChoiceData($search_condition_type) {
        return array(
            'key_name' => self::$search_checkbox_keys[$search_condition_type],
            'choices' => self::$search_checkbox_choices[$search_condition_type]
        );
    }

    /**
     * @param $search_condition_type
     * @return bool
     */
    public static function isCampaignCondition($search_condition_type) {
        return in_array($search_condition_type, self::$search_campaign_conditions);
    }

    /**
     * @param $condition
     * @return string
     */
    public static function toText($condition) {
        if (empty($condition)) {
            return '';
        }

        return implode('：', $condition);
    }
}