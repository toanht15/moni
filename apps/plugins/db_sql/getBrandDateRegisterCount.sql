SELECT DATE_FORMAT(created_at, '%Y/%m/%d') register_date,count(id) cnt
FROM brands_users_relations
WHERE brand_id = ?brand_id?
AND del_flg = 0
AND created_at BETWEEN ?from_date? AND ?to_date?
GROUP BY register_date