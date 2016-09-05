SELECT COUNT(*) comment_count
FROM (
    SELECT cu.id AS object_id, 1 AS object_type
    FROM comment_users cu
    WHERE cu.del_flg = 0
        AND cu.comment_plugin_id = ?comment_plugin_id?
    UNION
    SELECT cur.id AS object_id, 2 AS object_type
    FROM comment_user_replies cur
        LEFT JOIN comment_users cu
            ON cur.comment_user_id = cu.id AND cu.del_flg = 0
    WHERE cur.del_flg = 0
        AND cu.comment_plugin_id = ?comment_plugin_id?
) cu_data
    LEFT JOIN comment_user_relations cu_relation
        ON cu_data.object_id = cu_relation.object_id AND cu_data.object_type = cu_relation.object_type AND cu_relation.del_flg = 0
    LEFT JOIN brands_users_relations bur
        ON bur.user_id = cu_relation.user_id AND bur.withdraw_flg = 0 AND bur.del_flg = 0
WHERE cu_data.object_id IS NOT NULL
    AND bur.brand_id = ?brand_id?