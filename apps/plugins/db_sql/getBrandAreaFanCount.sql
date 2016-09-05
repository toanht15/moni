SELECT S.pref_id,count(R.id) cnt
FROM shipping_addresses S
INNER JOIN brands_users_relations R ON S.user_id = R.user_id AND R.brand_id = ?brand_id? AND R.del_flg = 0
WHERE S.del_flg = 0 AND S.pref_id != 0 AND R.created_at BETWEEN ?from_date? AND ?to_date?
AND R.withdraw_flg = 0 GROUP BY S.pref_id