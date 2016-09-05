SELECT c.id, c.brand_id, c.start_date, c.join_limit_flg, c.end_date
FROM cps c
WHERE c.del_flg = 0
AND c.type = 1
AND c.status = 3
AND (c.start_date BETWEEN ?start_date? AND NOW() OR c.end_date BETWEEN ?end_date? AND NOW())