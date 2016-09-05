SELECT
    MIN(p.id)
FROM instagram_hashtag_user_posts p
INNER JOIN instagram_hashtag_users u ON u.id = p.instagram_hashtag_user_id AND u.del_flg = 0
WHERE
    p.del_flg = 0
    AND p.id > ?instagram_hashtag_user_post_id?
    AND u.cp_action_id = ?cp_action_id?
    AND p.approval_status in(?approval_status?)