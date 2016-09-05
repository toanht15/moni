SELECT
  COUNT(cu.id) AS numbers
FROM
  brands br
INNER JOIN
  cps cp ON cp.brand_id = br.id AND cp.del_flg = 0
INNER JOIN
  cp_action_groups cag ON cag.cp_id = cp.id AND cag.del_flg = 0
INNER JOIN
  cp_actions ca ON ca.cp_action_group_id = cag.id AND ca.del_flg = 0
INNER JOIN
  cp_user_action_statuses st ON st.cp_action_id = ca.id AND st.del_flg = 0
INNER JOIN
  cp_users cu ON cu.id = st.cp_user_id AND cu.del_flg = 0
WHERE
  cu.del_flg = 0
  AND br.id = ?brand_id?
  AND br.test_page = 0
  AND cp.type = ?type?
  AND st.created_at < ?created_at_end?
  AND st.status = ?status?
  AND ca.type IN (?action_type?)
  AND ca.order_no = 1
  AND cag.order_no = 1