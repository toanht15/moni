SELECT
 count(brands.id) AS numbers
FROM
 brands
INNER JOIN brand_page_settings AS bps 
        ON brands.id = bps.brand_id  ?JOIN_CPS?

WHERE
 brands.test_page = 0
 AND bps.public_flg = 1 AND bps.del_flg = 0
 AND bps.created_at <= ?created_at?
