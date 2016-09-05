<?php

trait SearchFanTrait {
    public function getSearchProfileCondition($search_type, $search_no, $post, $nullable = false) {
        // アンケート等のキーは、[サーチタイプ/ID]で構成されているので、サーチタイプだけを取り出す。
        $split_search_key = explode('/', $post['search_type']);
        $search_condition = array();

        if ($split_search_key[0] <= CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {
            foreach ($post as $key => $value) {
//                if ($nullable && $value === '') {
//                    continue;
//                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO) {
                    if (preg_match('/^search_profile_member_no_from/', $key)) {
                        // 語尾がカンマだった場合は、語尾だけを外す
                        if (substr($value, -1) == ',') {
                            $search_condition[$key] = substr($value, 0, -1);
                        } else {
                            $search_condition[$key] = $value;
                        }
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD) {
                    if (preg_match('/^search_profile_register_period_/', $key)) {
                        $search_condition[$key] = $value;
                    }
                }

                if (preg_match('/^'.CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'\//', $search_type)) {
                    $social_media_id = explode('/', $search_type)[1];
                    if (preg_match('/^search_social_account\/'.$social_media_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;
                    }
                    if (($key == 'search_friend_count_from/'.$social_media_id || $key == 'search_friend_count_to/'.$social_media_id) &&
                        $post['search_social_account/'.$social_media_id.'/'.CpCreateSqlService::LINK_SNS.'/'.$search_no]) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM) {
                    if ($key == 'search_friend_count_sum_from' || $key == 'search_friend_count_sum_to' ||
                        $key == 'search_link_sns_count_from' || $key == 'search_link_sns_count_to') {
                        $search_condition[$key] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN) {
                    if (preg_match('/^search_profile_last_login_/', $key)) {
                        if ($value) {
                            if ($post['hh_'.$key.'/'.$search_no] && $post['mm_'.$key.'/'.$search_no]) {
                                $search_condition[$key] = $value.' '.$post['hh_'.$key.'/'.$search_no].':'.$post['mm_'.$key.'/'.$search_no].':00';
                            } else {
                                if (preg_match('/^search_profile_last_login_from/', $key)) {
                                    $search_condition[$key] = $value.' 00:00:00';
                                } else {
                                    $search_condition[$key] = $value.' 23:59:59';
                                }
                            }

                        }
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_SEX) {
                    if (preg_match('/^search_profile_sex\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0] . '/' . $split_key[1]] = $value;
                    }
                }


                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_RATE) {
                    if (preg_match('/^search_rate\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_ADDRESS) {
                    if (preg_match('/^search_profile_address/', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_AGE) {
                    if (preg_match('/^search_profile_age_/', $key)) {
                        if ($key == 'search_profile_age_not_set/'.$search_no) {
                            $search_condition['search_profile_age_not_set'] = $value;
                        } else {
                            $search_condition[$key] = $value;
                        }
                    }
                }

                if ($count_item = CpCreateSqlService::$search_count_item[$search_type]) {
                    if (preg_match('/^'.$count_item.'_/', $key)) {
                        $search_condition[$key] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO) {
                    if ($key == 'search_message_ratio_from' || $key == 'search_message_ratio_to') {
                        $search_condition[$key] = $value;
                    }
                }

                if ($search_type == CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS) {
                    if (preg_match('/^search_questionnaire_status\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }

                if (preg_match('/^' . CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE . '\//', $search_type)) {
                    $question_id = explode('/', $search_type)[1];
                    if (preg_match('/^search_profile_questionnaire\/'.$question_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0] . '/' . $split_key[1] . '/' . $split_key[2]] = $value;

                        if ($post['questionnaire_type/'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$split_key[1]]) {
                            $search_condition['questionnaire_type/'.$split_key[1]] = $post['questionnaire_type/'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$split_key[1]];
                        }
                    }
                }

                if (preg_match('/^' . CpCreateSqlService::SEARCH_IMPORT_VALUE.'\//', $search_type)) {
                    $definition_id = explode('/', $search_type)[1];
                    if (preg_match('/^search_import_value\/'.$definition_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;

                    }
                }

                if (preg_match('/^' . CpCreateSqlService::SEARCH_PROFILE_CONVERSION . '\//', $search_type)) {
                    $conversion_id = explode('/', $search_type)[1];
                    if ($key == 'search_profile_conversion_from/'.$conversion_id || $key == 'search_profile_conversion_to/'.$conversion_id ) {
                        $search_condition[$key] = $value;
                    }
                }

                if($split_search_key[0] == CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE) {
                    $social_type = $split_search_key[1];
                    $sns_account_id = $split_search_key[2];
                    if (preg_match('/^search_social_account_interactive\/'.$social_type.'\/'.$sns_account_id.'/', $key)) {
                        $search_condition[$key] = $value;
                    }
                     if (preg_match('/^search_tw_tweet_reply_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_tw_tweet_retweet_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_social_account_is_replied_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_social_account_is_retweeted_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_fb_posts_like_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_fb_posts_comment_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_social_account_is_liked_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_social_account_is_commented_count\/'.$social_type.'\/'.$sns_account_id.'/', $key)){
                        $search_condition[$key] = $value;
                    }

                }

                if ($search_type == CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {
                    if ($key == 'search_duplicate_address_from' || $key == 'search_duplicate_address_to' || $key == 'search_duplicate_address_by_cp_id' ) {
                        $search_condition[$key] = $value;
                    }
                    if (preg_match('/^search_duplicate_address\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;
                    }
                }
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
            $relation_id = explode('/', $search_type)[1];
            foreach($post as $key => $value) {
                if (preg_match('/^search_questionnaire\/'.$relation_id.'\//', $key)) {
                    $split_key = explode('/', $key);
                    // サーチ番号を除いてキーに入れる
                    $search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;

                    if ($post['questionnaire_type/'.CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$split_key[1]]) {
                        $search_condition['questionnaire_type/'.$split_key[1]] = $post['questionnaire_type/'.CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$split_key[1]];
                    }
                }

                if (preg_match('/^switch_type\//', $key)) {
                    $relation_id = explode('/', $search_type)[1];
                    if (preg_match('/^switch_type\/'.CpCreateSqlService::SEARCH_QUESTIONNAIRE.'\/'.$relation_id.'/', $key)) {
                        $search_condition[$key] = $value;
                    }
                }
            }
        }

        //TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
        if($search_type == CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD) {
            $relation_id = explode('/', $search_type)[1];

            foreach($post as $key => $value) {
                if (preg_match('/^search_child_birth_period_from\/'.$relation_id.'/', $key)) {
                    $search_condition['search_child_birth_period_from'.'/'.$relation_id] = $value;
                }
                if (preg_match('/^search_child_birth_period_to\/'.$relation_id.'/', $key)) {
                    $search_condition['search_child_birth_period_to'.'/'.$relation_id] = $value;
                }
            }
        }

        if ($nullable) {
            //FacebookMarketingの場合fromとtoは空のときにエラーを出ないように条件を消す
            $isNullCondition = true;
            foreach ($search_condition as $search_key => $search_value) {
                if (isset($search_value) && $search_value !== '') {
                    $isNullCondition = false;
                    break;
                }
            }
            if ($isNullCondition) {
                return array();
            }
        }

        return $search_condition;
    }

    public function resetSnsActionSearchCondition($session, $search_type, $sns_action_key) {
        if (isset($sns_action_key)) {
            foreach ($session[$search_type] as $key => $value) {
                if ($sns_action_key == CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE && preg_match('/^search_social_account_interactive\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_TW_TWEET_RETWEET_COUNT && preg_match('/^search_social_account_is_retweeted_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_TW_TWEET_RETWEET_COUNT && preg_match('/^search_tw_tweet_retweet_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_TW_TWEET_REPLY_COUNT && preg_match('/^search_tw_tweet_reply_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_TW_TWEET_REPLY_COUNT && preg_match('/^search_social_account_is_replied_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_FB_POSTS_LIKE_COUNT && preg_match('/^search_social_account_is_liked_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_FB_POSTS_LIKE_COUNT && preg_match('/^search_fb_posts_like_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_FB_POSTS_COMMENT_COUNT && preg_match('/^search_fb_posts_comment_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
                if ($sns_action_key == CpCreateSqlService::SEARCH_FB_POSTS_COMMENT_COUNT && preg_match('/^search_social_account_is_commented_count\//', $key)) {
                    unset($session[$search_type][$key]);
                }
            }
        } else {
            unset($session[$search_type]);
        }
        return $session;
    }

    /**
     * TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
     * 子供の生まれた年代から絞り込み条件に変換する
     * @param $search_condition
     * @param $search_type
     * @return mixed
     */
    public function convertChildBirthPeriodToSearchCondition ($search_condition, $search_type) {
        $question_relation_id = explode('/', $search_type)[1];

        /** @var ProfileQuestionProcessService $profile_question_process_service */
        $profile_question_process_service = $this->getService('ProfileQuestionProcessService');
        $condition = $profile_question_process_service->convertChildBirthPeriodToSearchCondition($search_condition['search_child_birth_period_from'.'/'.$question_relation_id], $search_condition['search_child_birth_period_to'.'/'.$question_relation_id], $question_relation_id);
        $search_condition["search_child_birth_period"."/".$question_relation_id] = $condition;

        return $search_condition;
    }
}