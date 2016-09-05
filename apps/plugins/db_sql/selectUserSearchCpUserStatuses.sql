SELECT
    D.id cp_id,
    D.order_no last_join_order_no,
    CP2.type last_join_action_type,
    D.created_at join_date,
    CP2.id cp_action_id
FROM
    (
    SELECT
        C.id id,
        MAX(CA.order_no) order_no,
        MAX(CEA.title) title,
        CUAS.status status,
        MAX(CUAS2.created_at) created_at,
        CA.id cp_action
    FROM
        users U
    INNER JOIN
        cp_users CU
        ON CU.user_id = U.id
        AND CU.del_flg = 0
    INNER JOIN
        cps C
        ON C.id = CU.cp_id
        AND C.status = 3
        AND C.type = 1
        AND C.del_flg = 0
    INNER JOIN
        cp_action_groups CAG
        ON CAG.cp_id = C.id
        AND CAG.order_no = 1
        AND CAG.del_flg = 0
    INNER JOIN
        cp_actions CA
        ON CA.cp_action_group_id = CAG.id
        AND CA.del_flg = 0
    LEFT OUTER JOIN
        cp_entry_actions CEA
        ON CEA.cp_action_id = CA.id
        AND CEA.del_flg = 0
    INNER JOIN
        cp_user_action_statuses CUAS
        ON CUAS.cp_action_id = CA.id
        AND CUAS.cp_user_id = CU.id
        AND CUAS.status = 1
        AND CUAS.del_flg = 0
    LEFT OUTER JOIN
        cp_user_action_statuses CUAS2
        ON CUAS2.cp_action_id = CA.id
        AND CAG.order_no = 1
        AND CA.order_no = 2
        AND CUAS2.cp_user_id = CU.id
        AND CUAS2.del_flg = 0
    WHERE
        U.monipla_user_id = ?platform_user_id?
    GROUP BY C.id
    ) D
INNER JOIN
    cps CP
    ON CP.id = D.id
    AND CP.del_flg = 0
INNER JOIN
    cp_action_groups CAGP
    ON CAGP.cp_id = CP.id
    AND CAGP.order_no = 1
    AND CAGP.del_flg = 0
INNER JOIN
    cp_actions CP2
    ON CP2.cp_action_group_id = CAGP.id
    AND CP2.order_no = D.order_no
    AND CP2.del_flg = 0
WHERE
    D.created_at is not null
