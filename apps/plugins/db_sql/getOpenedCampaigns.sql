SELECT count(cps.id) AS total
FROM cps
INNER JOIN brands ON cps.brand_id = brands.id
WHERE cps.status = ?status? AND brands.test_page != ?test_page? AND
      cps.start_date <= ?preriod_time? AND cps.end_date > ?preriod_time?