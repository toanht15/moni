SELECT c.name as conversion_name, bur.created_at as registered_date, buc.date_conversion, bur.no as relation_no
FROM brands_users_conversions buc
    INNER JOIN conversions c ON c.id = buc.conversion_id
      INNER JOIN brands_users_relations bur ON bur.brand_id = c.brand_id AND bur.user_id = buc.user_id
WHERE
  buc.del_flg = 0
AND
  bur.del_flg = 0
AND
  c.del_flg = 0
AND
  bur.withdraw_flg = 0
AND
  c.brand_id = ?brand_id?
AND
  c.id = ?conversion_id?
AND buc.date_conversion > bur.created_at        ?CONVERSION_AFTER_REGISTERED?