SELECT DISTINCT CAG.id group_id
  FROM cps C
 INNER JOIN cp_action_groups CAG ON CAG.cp_id = C.id AND CAG.del_flg = 0
 INNER JOIN cp_actions CA ON CA.cp_action_group_id = CAG.id AND CA.del_flg = 0
 WHERE C.id = ?cp_id?
   AND C.del_flg = 0
   AND (   EXISTS(SELECT 'x'
                    FROM cp_message_delivery_reservations DR
                   WHERE DR.cp_action_id = CA.id AND DR.status >= 3 AND DR.del_flg = 0)
        OR EXISTS(SELECT 'x'
                    FROM cp_user_action_messages CUM
                   WHERE CUM.cp_action_id = CA.id AND CUM.del_flg = 0)
       )