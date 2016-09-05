SELECT
COUNT(target.id) cnt
FROM
cp_message_delivery_targets target
INNER JOIN
brands_users_relations relate ON target.user_id = relate.user_id AND relate.brand_id = ?brand_id?
WHERE target.cp_message_delivery_reservation_id = ?reservation_id?
AND target.del_flg = 0
AND target.fix_target_flg = ?fix_target_flg?  ?FIX_TARGET?
AND relate.del_flg = 0
AND relate.withdraw_flg = 0