SELECT *
FROM
  ads_target_logs
WHERE
  ads_audiences_accounts_relation_id IN (?relation_ids?)
  AND del_flg = 0