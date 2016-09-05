SELECT
    cu.id 'cp_user_id',
    cu.created_at 'created_at',
    c.id 'cp_id',
    c.public_date 'public_date',
    c.start_date 'start_date',
    c.end_date 'end_date',
    c.announce_date 'announce_date',
    c.shipping_method 'shipping_method',
    ea.title 'title',
    b.id 'brand_id',
    b.name 'brand_name',
    b.directory_name 'directory_name',
    br.no 'no',
    u.id 'user_id',
    u.monipla_user_id 'monipla_user_id'
FROM
    cp_users cu
INNER JOIN
    cps c
    ON c.id = cu.cp_id
    AND c.type = 1
    AND c.status = 3
    AND c.del_flg = 0
INNER JOIN
    cp_action_groups cg
    ON cg.cp_id = c.id
    AND cg.order_no = 1
    AND cg.del_flg = 0
INNER JOIN
    cp_actions ca
    ON ca.cp_action_group_id = cg.id
    AND ca.order_no = 1
    AND ca.type = 0
    AND ca.del_flg = 0
INNER JOIN
    cp_entry_actions ea
    ON ea.cp_action_id = ca.id
    AND cg.del_flg = 0
INNER JOIN
    brands b
    ON b.id = c.brand_id
    AND b.test_page = 0
    AND b.del_flg = 0
INNER JOIN
    brands_users_relations br
    ON br.user_id = cu.user_id
    AND br.brand_id = b.id
    AND br.del_flg = 0
INNER JOIN
    users u
    ON u.id = cu.user_id
    AND u.monipla_user_id = ?platform_user_id?
    AND u.del_flg = 0
INNER JOIN
    cp_user_action_statuses cuas
    ON cuas.cp_user_id = cu.id
    AND cuas.status = 1
    AND cuas.del_flg = 0
WHERE
    b.test_page = 0
GROUP BY
    c.id
ORDER BY
    cu.id DESC