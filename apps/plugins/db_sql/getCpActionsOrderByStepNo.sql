SELECT A.id,A.cp_action_group_id,A.order_no,A.type,A.status
FROM cp_actions A
INNER JOIN cp_action_groups G ON A.cp_action_group_id = G.id
AND G.cp_id = ?cp_id?
AND G.del_flg = 0
WHERE A.del_flg = 0
ORDER BY G.order_no ASC, A.order_no ASC