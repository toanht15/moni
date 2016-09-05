SELECT pu.*
FROM photo_users pu
WHERE pu.del_flg = 0
    AND pu.id <= ?max_id?
    AND pu.approval_status = 1
    AND pu.cp_action_id IN (
        SELECT ca.id
        FROM cp_actions ca
            LEFT JOIN cp_action_groups cag
                ON cag.id = ca.cp_action_group_id
            LEFT JOIN content_api_codes cac
                ON cac.cp_id = cag.cp_id
        WHERE ca.del_flg = 0
            AND cag.del_flg = 0
            AND cac.del_flg = 0
            AND cac.code = ?code?
            AND cac.cp_action_type = ?cp_action_type?
            AND ca.type = ?cp_action_type?
    )