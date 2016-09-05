SELECT count(*) as cnt FROM (
  SELECT id
  FROM segment_provisions_users_relations S
  WHERE S.segment_provision_id IN (?provision_ids?)
  AND S.created_date IN (?created_dates?) ?SEARCH_BY_CREATED_DATE?
  AND S.del_flg = 0
  GROUP BY S.brands_users_relation_id
) q

