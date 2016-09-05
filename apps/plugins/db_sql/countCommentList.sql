SELECT COUNT(comment_data.id) comment_count
FROM (
        SELECT cu_relation.id
        FROM comment_user_relations cu_relation
            LEFT JOIN (
                SELECT cu.id AS object_id, cftu.text AS text, 1 AS object_type
                FROM comment_users cu
                    LEFT JOIN comment_free_text_users cftu
                        ON cftu.comment_user_id = cu.id AND cftu.del_flg = 0
                WHERE cu.del_flg = 0
                    AND cu.comment_plugin_id IN (?comment_plugin_ids?)
                UNION
                SELECT cur.id AS object_id, cur.text AS text, 2 AS object_type
                FROM comment_users cu
                    LEFT JOIN comment_user_replies cur
                        ON cur.comment_user_id = cu.id AND cur.del_flg = 0
                WHERE cu.del_flg = 0
                    AND cu.comment_plugin_id IN (?comment_plugin_ids?)
            ) cu_data
                ON cu_data.object_id = cu_relation.object_id AND cu_data.object_type = cu_relation.object_type
            LEFT JOIN user_public_profile_info uppi
                ON uppi.user_id = cu_relation.user_id AND uppi.del_flg = 0
            LEFT JOIN brands_users_relations bur
                ON bur.user_id = cu_relation.user_id AND bur.withdraw_flg = 0 AND bur.del_flg = 0
            LEFT JOIN comment_user_shares cu_share
                ON cu_share.comment_user_relation_id = cu_relation.id AND cu_share.del_flg = 0
        WHERE cu_relation.del_flg = 0
            AND cu_data.object_id IS NOT NULL
            AND cu_relation.status = ?status?
            AND cu_relation.discard_flg = ?discard_flg?
            AND TRIM(cu_relation.note) <> ""            ?NOTE_STATUS_VALID?
            AND TRIM(cu_relation.note) = ""             ?NOTE_STATUS_INVALID?
            AND cu_share.comment_user_relation_id IS NOT NULL   ?USE_SNS_SHARE?
            AND cu_share.comment_user_relation_id IS NULL       ?NOT_USE_SNS_SHARE?
            AND TRIM(cu_data.text) LIKE ?comment_content?       ?CONTENT_SEARCH?
            AND TRIM(uppi.nickname) LIKE ?nickname?             ?NICKNAME_SEARCH?
            AND cu_relation.no > 0                      ?SAVED_NO_FLG?
            AND bur.brand_id = ?brand_id?
            AND bur.no IN (?bur_no?)
            AND cu_relation.created_at >= ?from_date?   ?FROM_DATE_SEARCH?
            AND cu_relation.created_at <= ?to_date?     ?TO_DATE_SEARCH?
            AND cu_relation.created_at BETWEEN ?from_date? AND ?to_date?   ?PERIOD_SEARCH?
        GROUP BY cu_relation.id
    ) comment_data
