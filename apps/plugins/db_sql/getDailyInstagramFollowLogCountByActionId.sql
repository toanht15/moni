SELECT COUNT(*) as follow_count, DATE(created_at) as count_date
FROM cp_instagram_follow_user_logs
WHERE cp_action_id = ?cp_action_id?
AND follow_status = ?status?              ?USE_STATUS_CONDITION?
GROUP BY DATE(created_at)