SELECT
  c.id, c.status, c.start_date, c.end_date, c.brand_id, c.image_rectangle_url, c.image_url
FROM
  cps c
WHERE
  c.brand_id = ?brand_id?
  AND c.status IN (?statuses?) ?BY_STATUS?
  AND c.id IN (?ids?) ?BY_ID?
  AND c.del_flg = 0
  AND c.archive_flg = 0

