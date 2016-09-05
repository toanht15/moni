SELECT summed_date,value
FROM manager_brand_kpi_values
WHERE brand_id = ?brand_id?
AND column_id = ?column_id?
AND summed_date BETWEEN ?from_date? AND ?to_date? AND del_flg = 0