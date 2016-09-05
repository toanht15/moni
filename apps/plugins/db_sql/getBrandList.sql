SELECT
  b.*,
  bps.public_flg 'public_flg',
  bc.plan 'plan',
  kv1.value 'latest_fun_count',
  kv2.value 'second_fun_count',
  msm.name 'sales_name',
  mcm.name 'consultant_name'
FROM
  brands b
INNER JOIN brands_agents ba ON ba.manager_id = ?manager_id? AND b.id = ba.brand_id ?IS_AGENT?
INNER JOIN
  brand_page_settings bps ON b.id = bps.brand_id
  AND bps.del_flg = 0
  AND bps.public_flg = ?public_flg? ?ACCESS?
INNER JOIN
  brand_contracts bc ON b.id = bc.brand_id
  AND bc.del_flg = 0
  AND bc.plan = ?plan? ?PLAN?
INNER JOIN
  sales_managers sm ON b.id = sm.brand_id
  AND sm.del_flg = 0
INNER JOIN
  consultants_managers cm ON b.id = cm.brand_id
  AND cm.del_flg = 0
LEFT JOIN
  manager_brand_kpi_values kv1 ON b.id = kv1.brand_id
  AND kv1.del_flg = 0
  AND kv1.column_id = ?column_id?
  AND kv1.summed_date = ?today_date?
LEFT JOIN
  manager_brand_kpi_values kv2 ON b.id = kv2.brand_id
  AND kv2.del_flg = 0
  AND kv2.column_id = ?column_id?
  AND kv2.summed_date = ?yesterday_date?
LEFT JOIN
  managers msm ON sm.sales_manager_id = msm.id
  AND msm.del_flg = 0
LEFT JOIN
  managers mcm ON cm.consultants_manager_id = mcm.id
  AND mcm.del_flg = 0
WHERE
  b.del_flg = 0
  AND b.name LIKE ?search_brand_name? ?SEARCH_BRAND_NAME?
  AND b.test_page = ?test_page? ?ACCOUNT?
  AND msm.id = ?sales_manager_id? ?SALES?
  AND mcm.id = ?consultants_manager_id? ?CONSULTANT?
  AND bc.delete_status = ?delete_status? ?DELETE_STATUS?
ORDER BY
  b.id DESC