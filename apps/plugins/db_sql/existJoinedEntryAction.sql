SELECT 1
  FROM
    cp_user_action_statuses cas INNER JOIN cp_actions ca ON cas.cp_action_id = ca.id INNER JOIN cp_action_groups cag ON ca.cp_action_group_id = cag.id
  WHERE
    cag.cp_id = ?CP_ID? AND cag.order_no = 1 AND cag.del_flg = 0 AND ca.order_no = 1 AND ca.del_flg = 0 AND cas.cp_user_id = ?CP_USER_ID? AND cas.status = 1 AND cas.del_flg = 0 LIMIT 1