SELECT
  count(DISTINCT brands.id) AS total
FROM
  brands
INNER JOIN
  segment_action_logs ON segment_action_logs.brand_id = brands.id AND (segment_action_logs.created_at  BETWEEN ?created_at_start? AND ?created_at_end?)
INNER JOIN
  brand_contracts ON brand_contracts.brand_id = brands.id AND (NOW() BETWEEN brand_contracts.contract_start_date AND brand_contracts.contract_end_date)
WHERE
  brands.del_flg = 0
  AND brands.test_page = 0
  AND segment_action_logs.del_flg = 0
  AND segment_action_logs.type = ?type?