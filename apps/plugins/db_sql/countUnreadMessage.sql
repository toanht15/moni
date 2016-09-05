SELECT COUNT(*)
FROM cps c INNER JOIN cp_users cu ON c.id = cu.cp_id INNER JOIN cp_user_action_messages cuam ON cu.id = cuam.cp_user_id
WHERE
	c.brand_id = ?BRAND_ID? AND c.status IN(2, 3, 4) AND c.del_flg = 0
	AND cu.user_id = ?USER_ID? AND cu.del_flg = 0
	AND cuam.read_flg = 0 AND cuam.del_flg = 0