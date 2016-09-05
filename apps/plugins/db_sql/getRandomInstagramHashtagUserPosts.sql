SELECT
    *
FROM
    instagram_hashtag_user_posts AS post,
    (
        SELECT
            post.id AS random_id
        FROM
            instagram_hashtag_users user
        INNER JOIN instagram_hashtag_user_posts post ON user.id = post.instagram_hashtag_user_id AND user.cp_action_id = ?cp_action_id?
        WHERE
            user.del_flg = 0
            AND post.approval_status = 1
        ORDER BY RAND() LIMIT 0, ?limit?
    ) AS random
WHERE
    post.del_flg = 0
    AND post.id = random.random_id LIMIT 0 , ?limit?