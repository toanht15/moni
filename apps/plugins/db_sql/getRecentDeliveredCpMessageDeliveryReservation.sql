SELECT cp_action_id
FROM cp_message_delivery_reservations cmdr
WHERE NOT EXISTS (SELECT * FROM cp_lost_notifications cln WHERE cln.cp_action_id = cmdr.cp_action_id )
AND delivery_type <> ?exclude_delivery_type?
AND updated_at > ?updated_at_begin?
AND status = ?status?
AND del_flg = 0