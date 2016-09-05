SELECT
  c.*, b.app_id, b.enterprise_id, b.name, b.profile_img_url, b.directory_name
FROM
  cps c
INNER JOIN brands b ON b.id = c.brand_id AND
  b.del_flg = 0
INNER JOIN brand_page_settings p ON b.id = p.brand_id AND
  p.del_flg = 0 AND
  p.public_flg = 1
WHERE
  b.test_page = 0 AND
  c.status = 3 AND
  c.end_date > now() AND
  c.type = 1 AND
  b.app_id IN (?app_id?)