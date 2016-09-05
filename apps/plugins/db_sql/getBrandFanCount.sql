SELECT count(R.id) cnt
FROM brands_users_relations R
WHERE R.brand_id = ?brand_id?
AND R.del_flg = 0
AND R.withdraw_flg = 0
AND R.created_at BETWEEN ?from_date? AND ?to_date?