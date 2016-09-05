SELECT
cp_actions.id,
cps.type,
brands.id brand_id,
brands.name,
brands.directory_name,
(SELECT COUNT(*) FROM cp_message_delivery_targets target WHERE target.cp_action_id = cp_actions.id) target_count,
rsv.updated_at,
(SELECT COUNT(*) FROM open_email_tracking_logs open_log WHERE open_log.cp_action_id = cp_actions.id) open_log_count,
(SELECT COUNT(*) FROM clicked_email_link_logs click_log WHERE click_log.cp_action_id = cp_actions.id) click_log_count,
cps.id cp_id

FROM (SELECT cp_action_id, updated_at, id FROM cp_message_delivery_reservations WHERE status = 5 AND del_flg = 0) rsv

INNER JOIN cp_actions ON rsv.cp_action_id = cp_actions.id AND cp_actions.del_flg = 0

INNER JOIN cp_action_groups grp ON cp_actions.cp_action_group_id = grp.id AND grp.del_flg = 0

INNER JOIN cps ON cps.id = grp.cp_id AND cps.del_flg = 0

INNER JOIN brands ON brands.id = cps.brand_id AND brands.del_flg = 0

INNER JOIN brand_contracts bc ON bc.brand_id = brands.id AND bc.del_flg = 0

WHERE cps.status != 1

AND brands.test_page = 0                    ?TEST_PAGE?

AND cp_actions.id = ?message?               ?MESSAGE?

AND brands.id = ?client_id?                 ?CLIENT?

AND rsv.updated_at >= ?delivered_date_from?               ?FROM_DATE?

AND rsv.updated_at <= ?delivered_date_to?                 ?TO_DATE?

AND bc.delete_status = 0

GROUP BY cp_actions.id

ORDER BY cp_actions.id DESC

LIMIT 20                                 ?IS_LIMIT?
