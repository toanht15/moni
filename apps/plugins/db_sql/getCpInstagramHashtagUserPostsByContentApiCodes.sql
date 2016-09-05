SELECT post.*
FROM instagram_hashtag_user_posts post
INNER JOIN instagram_hashtag_users user ON user.id = post.instagram_hashtag_user_id
WHERE post.del_flg = 0
    AND post.id <= ?max_id? ?BY_MAX_ID?
    AND post.id > ?min_id? ?BY_MIN_ID?
    AND post.approval_status = 1
    AND user.cp_action_id IN (
        SELECT ca.id
        FROM cp_actions ca
            LEFT JOIN cp_action_groups cag
                ON cag.id = ca.cp_action_group_id
            LEFT JOIN content_api_codes cac
                ON cac.cp_id = cag.cp_id
        WHERE ca.del_flg = 0
            AND cag.del_flg = 0
            AND cac.del_flg = 0
            AND cac.code IN (?codes?)
            AND cac.cp_action_type = ?cp_action_type?
            AND ca.type = ?cp_action_type?
    )