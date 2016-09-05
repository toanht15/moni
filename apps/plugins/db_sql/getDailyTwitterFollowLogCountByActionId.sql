SELECT COUNT(*) as follow_count, DATE(fl.created_at) as count_date
FROM cp_twitter_follow_logs fl
  INNER JOIN cp_twitter_follow_actions fa ON fa.id = fl.action_id
WHERE
  fl.del_flg = 0
AND fa.del_flg = 0
AND fa.cp_action_id = ?cp_action_id?
AND fl.status = ?status?              ?USE_STATUS_CONDITION?
GROUP BY DATE(fl.created_at)