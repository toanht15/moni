SELECT DATE(updated_at) as count_date,COUNT(*) as user_count
FROM cp_user_action_statuses
WHERE
del_flg = 0
AND status = 1
AND cp_action_id = ?cp_action_id?
AND device_type = ?device_type?         ?USE_DEVICE_TYPE_CONDITION?
GROUP BY DATE(updated_at)