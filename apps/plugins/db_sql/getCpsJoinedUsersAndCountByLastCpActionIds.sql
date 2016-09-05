SELECT u.id as user_id
FROM users u
  LEFT JOIN cp_users cu ON cu.user_id = u.id
    LEFT JOIN cp_user_action_statuses cuas ON cuas.cp_user_id = cu.id
WHERE cuas.cp_action_id IN (?cp_action_ids?)
AND u.del_flg = 0
AND cu.del_flg = 0
AND cuas.del_flg = 0
AND cuas.status = ?status?
GROUP BY u.id
HAVING COUNT(*) >= ?cp_joined_count?