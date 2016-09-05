SELECT
    count(DISTINCT brands.id) AS total
FROM
        brands
INNER JOIN
        brand_options ON brand_options.brand_id = brands.id AND brand_options.option_id = ?option_id?
INNER JOIN
        brand_contracts ON brand_contracts.brand_id = brands.id AND (NOW() BETWEEN brand_contracts.contract_start_date AND brand_contracts.contract_end_date)
WHERE
        brands.del_flg = 0 
    AND brands.test_page = 0
    AND brand_options.del_flg = 0

