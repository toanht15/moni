SELECT DATE(cuas.updated_at) as count_date,COUNT(*) user_count
FROM cp_user_action_statuses cuas
    INNER JOIN cp_users cu ON cu.id = cuas.cp_user_id
        INNER JOIN cps c ON c.id = cu.cp_id
            INNER JOIN brands_users_relations bur ON bur.user_id = cu.user_id
WHERE cuas.del_flg = 0
AND cu.del_flg = 0
AND c.del_flg = 0
AND bur.del_flg = 0
AND cuas.cp_action_id = ?cp_action_id?
AND bur.brand_id = ?brand_id?
AND cuas.status = 1
AND bur.created_at >= c.start_date  ?NEW_REGISTERED_USER?
AND bur.created_at < c.start_date  ?ALREADY_FAN_USER?
GROUP BY DATE(cuas.updated_at)