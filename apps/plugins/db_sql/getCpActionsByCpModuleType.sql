SELECT
    C.id cp_id, CA.*
FROM cps C
INNER JOIN cp_action_groups G ON G.cp_id = C.id AND G.del_flg = 0
INNER JOIN cp_actions CA ON CA.cp_action_group_id = G.id AND CA.del_flg = 0
WHERE
    C.del_flg = 0
    AND C.status in(?status?)
    AND (C.permanent_flg = 1 OR C.end_date > ?end_date?)
    AND (C.permanent_flg = 1 OR C.announce_date > ?announce_date?)
    AND C.id = ?cp_id?
    AND CA.type = ?module_type?