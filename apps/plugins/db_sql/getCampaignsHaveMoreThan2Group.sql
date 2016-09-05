SELECT c.id
FROM cps c
INNER JOIN  cp_action_groups g ON g.cp_id = c.id
WHERE c.del_flg = 0 AND g.del_flg = 0
AND c.type = 1
AND c.join_limit_flg = 0
AND c.fix_basic_flg = 1
AND c.end_date >= CURDATE() + '00:00:00'  ?LIMIT_MODE?
AND c.end_date <= CURDATE() + '13:00:00'  ?LIMIT_MODE?
GROUP BY c.id
HAVING COUNT(c.id) >= 2;