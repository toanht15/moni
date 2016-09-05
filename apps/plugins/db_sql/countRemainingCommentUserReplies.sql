SELECT COUNT(cur.id) reply_count
FROM comment_user_relations cu_relation
    LEFT JOIN comment_user_replies cur
        ON cu_relation.object_id = cur.id AND cur.del_flg = 0
WHERE cu_relation.del_flg = 0
    AND cu_relation.object_type = 2
    AND cu_relation.status = 1
    AND cur.comment_user_id = ?comment_user_id?
    AND cu_relation.id <> ?exclude_id?
    AND cu_relation.id < ?prev_min_id?