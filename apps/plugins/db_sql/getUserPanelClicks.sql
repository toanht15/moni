SELECT
  *, count(*) access_count
FROM
  user_panel_clicks
WHERE
  del_flg = 0
  AND created_at > ?start_date?
  AND created_at <= ?end_date?
GROUP BY
  entries, entries_id