SELECT COUNT(*)
FROM cps
WHERE
public_date BETWEEN ?start_date? AND ?end_date?
AND (end_date > NOW() OR permanent_flg = 1)
AND brand_id = ?brand_id?
AND status = ?status?
AND del_flg = 0