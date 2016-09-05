SELECT
    count(DISTINCT brands.id) AS total
FROM
        brands
INNER JOIN
        segments ON segments.brand_id = brands.id AND segments.archive_flg = ?archive_flg?
INNER JOIN
        brand_contracts ON brand_contracts.brand_id = brands.id AND (NOW() BETWEEN brand_contracts.contract_start_date AND brand_contracts.contract_end_date)
WHERE
        brands.del_flg = 0 
    AND brands.test_page = 0
    AND segments.status = ?status?
    AND segments.del_flg = 0

