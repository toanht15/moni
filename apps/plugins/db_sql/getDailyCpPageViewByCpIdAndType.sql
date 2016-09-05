SELECT summed_date as count_date, total_view_count, pc_view_count, sp_view_count, tablet_view_count, user_count
FROM cp_page_views
WHERE
  del_flg = 0
AND
  cp_id = ?cp_id?
AND
  type = ?page_view_type?
GROUP BY summed_date
