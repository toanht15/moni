SELECT
  a.id audience_id,
  a.name audience_name,
  a.description audience_description,
  a.search_condition audience_search_condition,
  a.search_type audience_search_type,
  a.status audience_status,
  r.id relation_id,
  r.ads_account_id account_id,
  r.sns_audience_id sns_audience_id,
  r.type type,
  r.auto_send_target_flg auto_send_target_flg
FROM
  ads_audiences a
LEFT OUTER JOIN ads_audiences_accounts_relations r ON a.id = r.ads_audience_id AND r.type < 2 AND r.del_flg = 0
WHERE
  a.del_flg = 0
  AND a.brand_user_relation_id = ?brand_user_relation_id?
  AND r.ads_account_id IN (?target_account_ids?) ?SEARCH_BY_ACCOUNT?