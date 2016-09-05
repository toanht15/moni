SELECT
  (SELECT COUNT(*) FROM cp_user_action_messages WHERE cp_user_id = ?CP_USER_ID? AND del_flg = 0) msg_count,
  (SELECT COUNT(*) FROM cp_actions ca JOIN cp_action_groups cag ON cag.id = ca.cp_action_group_id WHERE ca.id = ?CP_ACTION_ID? AND ca.del_flg = 0 AND ca.type IN (0, 5) AND ca.order_no = 1 AND cag.order_no = 1 AND cag.del_flg = 0) opening_action_count,
  (SELECT personal_info_flg FROM brands_users_relations WHERE brand_id =  ?BRAND_ID? AND user_id = ?USER_ID? AND del_flg = 0 AND withdraw_flg = 0 ) personal_info_flg,
  (SELECT COUNT(*) FROM cp_profile_questionnaires WHERE del_flg = 0 AND cp_action_id = ?CP_ACTION_ID?) questionnaire_count
FROM dual