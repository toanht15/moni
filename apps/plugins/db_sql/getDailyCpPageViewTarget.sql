SELECT c.*
FROM cps c
	LEFT JOIN cp_page_view_logs cpl ON cpl.cp_id = c.id
WHERE
	c.del_flg = 0
AND (c.status = 3 OR c.status = 5)
AND c.type = 1
AND (cpl.status IS NULL OR cpl.status <> 3)
AND DATE(c.start_date) BETWEEN ?begin_date? AND ?start_date?