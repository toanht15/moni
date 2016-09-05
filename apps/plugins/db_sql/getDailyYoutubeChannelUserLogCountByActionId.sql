SELECT COUNT(*) as follow_count, DATE(created_at) as count_date
FROM cp_youtube_channel_user_logs
WHERE
  del_flg = 0
AND cp_action_id = ?cp_action_id?
AND status = ?status?             ?USE_STATUS_CONDITION?
GROUP BY DATE(created_at)