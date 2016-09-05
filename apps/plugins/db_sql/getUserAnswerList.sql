SELECT bur.id bur_id, bur.no, u.name, u.profile_image_url, cuas.created_at, qua.approval_status
FROM cp_user_action_statuses cuas
    LEFT JOIN cp_users cu
        ON cu.id = cuas.cp_user_id AND cu.del_flg = 0
    LEFT JOIN brands_users_relations bur
        ON bur.user_id = cu.user_id AND bur.del_flg = 0
    LEFT JOIN users u
        ON cu.user_id = u.id AND u.del_flg = 0
    LEFT JOIN questionnaire_user_answers qua
        ON qua.brands_users_relation_id = bur.id AND qua.del_flg = 0 AND qua.cp_action_id = cuas.cp_action_id
WHERE cuas.del_flg = 0
    AND cuas.status = 1
    AND bur.brand_id = ?brand_id?
    AND cuas.cp_action_id = ?cp_action_id?
    AND qua.approval_status = ?approval_status?                                     ?POST_APPROVAL?
    AND qua.approval_status IS NULL                                                 ?PRE_APPROVAL?
ORDER BY cuas.created_at desc  ?CREATED_AT_DESC?
ORDER BY cu.id desc     ?CP_USER_ID_DESC?
