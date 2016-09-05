SELECT
    COUNT(DISTINCT cu.id) AS total
FROM
    brands br
        INNER JOIN
    cps cp ON cp.brand_id = br.id AND cp.del_flg = 0
        INNER JOIN
    cp_action_groups cag ON cag.cp_id = cp.id AND cag.del_flg = 0
        INNER JOIN
    cp_actions ca ON ca.cp_action_group_id = cag.id
        AND ca.del_flg = 0
        INNER JOIN
    cp_user_action_statuses st ON st.cp_action_id = ca.id
        AND st.del_flg = 0
        INNER JOIN
    cp_users cu ON cu.id = st.cp_user_id AND cu.del_flg = 0
        INNER JOIN
    cp_user_action_messages cuam ON cuam.cp_user_id = cu.id
WHERE
            br.test_page = 0
        AND br.del_flg = 0
        AND cp.type = 1
        AND cp.join_limit_flg = 0
        AND ca.type = 9
        AND st.updated_at >= ?created_at_start?
        AND st.updated_at <= ?created_at_end?
        AND st.status = 1
        AND cuam.read_flg = 1