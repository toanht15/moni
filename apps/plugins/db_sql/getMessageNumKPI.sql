SELECT
  COUNT(cu.id) AS numbers
FROM
  cp_users cu
INNER JOIN
  cps cp ON cu.cp_id = cp.id AND cp.del_flg = 0
INNER JOIN
  brands br ON br.id = cp.brand_id AND br.del_flg = 0
WHERE
  cu.del_flg = 0
  AND br.id = ?brand_id?
  AND br.test_page = 0
  AND cp.type = ?type?
  AND cu.created_at >= ?created_at_start?
  AND cu.created_at <= ?created_at_end?