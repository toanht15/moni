SELECT COUNT(cu.id) comment_count
FROM comment_user_relations cu_relation
    LEFT JOIN comment_users cu
        ON cu_relation.object_id = cu.id AND cu.del_flg = 0
WHERE cu_relation.del_flg = 0
    AND cu_relation.object_type = 1
    AND cu_relation.status = 1
    AND cu.comment_plugin_id = ?comment_plugin_id?
    AND cu_relation.id < ?prev_min_id?
