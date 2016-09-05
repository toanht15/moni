UPDATE cp_user_action_statuses
  SET status = 1
  , user_agent = ?USER_AGENT?
  , device_type = ?DEVICE_TYPE?
  WHERE
  cp_user_id =?CP_USER_ID?
   AND cp_action_id = ?CP_ACTION_ID?
   AND del_flg = 0