SELECT u.id as user_id
  FROM cp_users cu
    INNER JOIN cps cp ON cp.id = cu.cp_id
      INNER JOIN users u ON u.id = cu.user_id
WHERE cu.del_flg = 0
AND cp.del_flg = 0
AND u.del_flg = 0
AND u.created_at > cp.start_date          ?GET_NEW_CREATED_USER?
AND cp.id = ?cp_id?
AND NOT EXISTS (SELECT * FROM cp_lost_notification_users clnu WHERE clnu.user_id = u.id )         ?EXCLUDE_CP_LOST_DELIVERY_USERS?
AND u.id NOT IN (?cp_winners?)           ?EXCLUDE_CP_WINNER?
