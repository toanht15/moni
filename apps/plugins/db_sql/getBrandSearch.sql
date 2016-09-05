select
brands.*,bps.public_flg,sm.sales_manager_id,cm.consultants_manager_id
FROM
brands
LEFT JOIN
brand_page_settings bps ON brands.id = bps.brand_id
LEFT JOIN sales_managers sm ON brands.id = sm.brand_id
LEFT JOIN consultants_managers cm ON brands.id = cm.brand_id
where brands.del_flg = 0
AND public_flg IS NULL                              ?PUBLIC_FLG_NULL?
AND public_flg = ?type?                             ?PUBLIC_FLG?
AND test_page = ?test_page?                         ?TEST?
AND sales_manager_id = ?sales_manager?              ?SALES?
AND consultants_manager_id = ?consultants_manager?  ?CONSULTANTS?
ORDER BY brands.created_at DESC 