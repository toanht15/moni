SELECT
     B.id brand_id, R.id reservation_id,R.cp_action_id cp_action_id
FROM
    cp_message_delivery_reservations R
INNER JOIN
    cp_actions A ON A.id = R.cp_action_id AND A.del_flg = 0
INNER JOIN
    cp_action_groups G ON G.id = A.cp_action_group_id AND G.del_flg = 0
INNER JOIN
    cps C ON C.id = G.cp_id AND C.del_flg = 0
INNER JOIN
    brands B ON B.id = C.brand_id AND B.del_flg = 0
WHERE
    R.del_flg = 0
    AND R.status = 5
    AND R.updated_at BETWEEN ?from_date? AND ?to_date?
