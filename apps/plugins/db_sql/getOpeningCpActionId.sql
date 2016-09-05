SELECT
    ca.id
FROM brands br
    INNER JOIN cps cp ON cp.brand_id = br.id AND cp.del_flg = 0
    INNER JOIN cp_action_groups cag ON cag.cp_id = cp.id AND cag.del_flg = 0
    INNER JOIN cp_actions ca ON ca.cp_action_group_id = cag.id AND ca.del_flg = 0
WHERE br.del_flg = 0
    AND br.test_page = 0
    ANd br.id = ?brand_id?
    AND cp.type = 1
    AND ca.type IN (?action_type?)
    AND ca.order_no = 1
    AND cag.order_no = 1
