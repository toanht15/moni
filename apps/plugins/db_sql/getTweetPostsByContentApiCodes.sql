SELECT tm.*, sa.social_media_account_id as twitter_account_id, sa.name as screen_name, sa.profile_page_url as profile_page_url, cta.tweet_fixed_text
FROM tweet_messages tm
    LEFT JOIN cp_users cu
        ON cu.id = tm.cp_user_id
    LEFT JOIN social_accounts sa
        ON cu.user_id = sa.user_id
    LEFT JOIN cp_tweet_actions cta
        ON cta.id = tm.cp_tweet_action_id
WHERE tm.del_flg = 0
    AND cu.del_flg = 0
    AND sa.del_flg = 0
    AND cta.del_flg = 0
    AND sa.social_media_id = 3
    AND tm.id <= ?max_id?
    AND tm.tweet_content_url <> ''
    AND tm.approval_status = 1
    AND tm.cp_tweet_action_id IN (
        SELECT cta.id
        FROM cp_tweet_actions cta
            LEFT JOIN cp_actions ca
                ON ca.id = cta.cp_action_id
            LEFT JOIN cp_action_groups cag
                ON cag.id = ca.cp_action_group_id
            LEFT JOIN content_api_codes cac
                ON cac.cp_id = cag.cp_id
        WHERE cta.del_flg = 0
            AND ca.del_flg = 0
            AND cag.del_flg = 0
            AND cac.del_flg =0
            AND cac.code = ?code?
            AND cac.cp_action_type = ?cp_action_type?
            AND ca.type = ?cp_action_type?
            AND ca.id IN (?cur_action_id?)              ?SEARCH_BY_ACTION_IDS?
    )