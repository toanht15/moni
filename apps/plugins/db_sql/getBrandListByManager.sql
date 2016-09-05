SELECT b.*
FROM brands b
INNER JOIN
  sales_managers sm ON b.id = sm.brand_id
  AND sm.del_flg = 0
INNER JOIN
  consultants_managers cm ON b.id = cm.brand_id
  AND cm.del_flg = 0
INNER JOIN
  brand_contracts bc ON bc.brand_id = b.id
  AND ( bc.plan = 1 OR bc.plan = 2 )
  AND bc.operation != 1
  AND bc.delete_status != 3
  AND bc.for_production_flg = 1
WHERE
  b.del_flg = 0
  AND sm.sales_manager_id = ?sales_manager_id? ?SALES?
  AND cm.consultants_manager_id = ?consultants_manager_id? ?CONSULTANT?
  AND b.test_page = 0
ORDER BY
  b.id DESC