SELECT
    COUNT(cu.id) AS numbers
FROM cp_user_action_statuses st
    INNER JOIN cp_users cu ON cu.id = st.cp_user_id AND cu.del_flg = 0
WHERE st.del_flg = 0
    AND st.cp_action_id IN (?ca_ids?)
    AND st.updated_at >= ?updated_at_start?
    AND st.updated_at < ?updated_at_end?
    AND st.status = 1
