SELECT rl.login_date, rl.device, b.no as relation_no
FROM redirector_logs rl
  INNER JOIN redirectors r ON r.id = rl.redirector_id
	  LEFT JOIN brands_users_relations b ON rl.brand_id = b.brand_id AND rl.user_id = b.user_id
WHERE
  r.del_flg = 0
AND
	r.brand_id = ?brand_id?
AND
	r.id = ?redirector_id?