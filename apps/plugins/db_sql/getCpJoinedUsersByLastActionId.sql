SELECT u.id as user_id
FROM users u
  LEFT JOIN cp_users cu ON cu.user_id = u.id
    LEFT JOIN cp_user_action_statuses cuas ON cuas.cp_user_id = cu.id
      LEFT JOIN brands_users_relations bur ON bur.user_id = u.id
WHERE cuas.cp_action_id = ?cp_action_id?
AND u.del_flg = 0
AND cu.del_flg = 0
AND cuas.del_flg = 0
AND bur.withdraw_flg = 0
AND cuas.status = ?status?
AND u.id NOT IN (?user_ids?)
ORDER BY RAND() LIMIT ?limit?