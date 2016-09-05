SELECT cps.id campaign_id
FROM static_html_stamp_rally_campaigns shsrc
  LEFT JOIN cps ON cps.id = shsrc.campaign_id
    LEFT JOIN brands b ON b.id = cps.brand_id
WHERE cps.del_flg = 0
AND b.del_flg = 0
AND b.id = ?brand_id?
AND cps.end_date < NOW()
AND cps.end_date BETWEEN ?start_date? AND ?end_date?