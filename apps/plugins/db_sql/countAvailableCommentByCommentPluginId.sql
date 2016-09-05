SELECT COUNT(*) comment_count
FROM comment_users cu
INNER JOIN comment_user_relations cu_relation
        ON cu.id = cu_relation.object_id
WHERE cu_relation.del_flg = 0
AND cu.del_flg = 0
AND cu_relation.discard_flg = 0
AND cu_relation.object_type = 1
AND cu.comment_plugin_id = ?comment_plugin_id?