SELECT
    cu1.user_id,
    cu1.cp_id as activated_by_cp_id,
    cuas1.updated_at as activated_at,
    cuas2.last_activated_at,
    (
        SELECT
            MAX(cuas.updated_at)
        FROM
            cp_user_action_statuses cuas
            INNER JOIN
                cp_users cu
            ON  cuas.cp_user_id = cu.id
            INNER JOIN
                cps cp
            ON  cu.cp_id = cp.id
            INNER JOIN
                cp_actions ca
            ON  cuas.cp_action_id = ca.id
            INNER JOIN
                cp_action_groups cag
            ON  ca.cp_action_group_id = cag.id
        WHERE
            cuas.del_flg = 0
        AND cu.del_flg = 0
        AND cp.del_flg = 0
        AND ca.del_flg = 0
        AND cag.del_flg = 0
        AND cu.user_id = cu1.user_id
        AND cuas.updated_at < ?date_from?
        AND cuas.status = 1
        AND cp.type IN(1, 5)
        AND ca.order_no = 1
        AND cag.order_no = 1
    ) previous_activated_at,
    gus.status
FROM
    cp_user_action_statuses cuas1
    INNER JOIN
        (
            SELECT
                MIN(cuas.id) as id,
                MAX(cuas.updated_at) as last_activated_at
            FROM
                cp_user_action_statuses cuas
                INNER JOIN
                    cp_users cu
                ON  cuas.cp_user_id = cu.id
                INNER JOIN
                    cp_actions ca
                ON  cuas.cp_action_id = ca.id
            WHERE
                cuas.del_flg = 0
            AND cu.del_flg = 0
            AND cuas.updated_at >= ?date_from?
            AND cuas.updated_at < ?date_to?
            AND cuas.status = 1
            AND ca.id IN ( ?cp_action_ids? )
            GROUP BY
                cu.user_id
        ) cuas2
    ON  cuas1.id = cuas2.id
    INNER JOIN
        cp_users cu1
    ON  cuas1.cp_user_id = cu1.id
    LEFT OUTER JOIN
        growth_user_stats gus
    ON  cu1.user_id = gus.user_id