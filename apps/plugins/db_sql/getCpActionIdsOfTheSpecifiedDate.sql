SELECT
    ca.id
FROM
    cp_actions ca
    INNER JOIN
        cp_action_groups cag
    ON  ca.cp_action_group_id = cag.id
    INNER JOIN
        cps cp
    ON  cag.cp_id = cp.id
WHERE
    ca.del_flg = 0
AND cag.del_flg = 0
AND cp.del_flg = 0
AND ca.order_no = 1
AND cag.order_no = 1
AND cp.type IN(1, 5)
AND cp.start_date < ?target_next_date?
AND cp.end_date >= ?target_date?