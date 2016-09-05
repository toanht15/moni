SELECT bur.id bur_id, cuas.created_at, cuas.id finished_answer_id
FROM brands_users_relations bur
    LEFT JOIN cp_users cu
        ON cu.user_id = bur.user_id AND cu.del_flg = 0
    LEFT JOIN cp_user_action_statuses cuas
        ON cuas.cp_user_id = cu.id AND cuas.del_flg = 0
WHERE bur.del_flg = 0
    AND bur.id IN (?bur_ids?)
    AND cuas.cp_action_id = ?next_action_id?