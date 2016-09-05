SELECT DATE_FORMAT(W.created_at, '%Y/%m/%d') withdraw_date,count(W.id) cnt
FROM withdraw_logs W
INNER JOIN brands_users_relations R
ON R.id = W.brand_user_relation_id
AND R.brand_id = ?brand_id?
AND R.del_flg = 0
AND R.withdraw_flg = 1
WHERE W.del_flg = 0 AND W.created_at BETWEEN ?from_date? AND ?to_date?
GROUP BY withdraw_date