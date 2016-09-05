SELECT I.sex,count(R.id) cnt
FROM user_search_info I
INNER JOIN brands_users_relations R ON I.user_id = R.user_id
AND R.brand_id = ?brand_id? AND R.del_flg = 0
WHERE I.del_flg = 0 AND R.created_at BETWEEN ?from_date? AND ?to_date? AND R.withdraw_flg = 0
GROUP BY I.sex