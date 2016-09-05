SELECT
    count(DISTINCT R.question_id) AS total
FROM
    cp_questionnaire_actions QA
INNER JOIN
    cp_actions ACT ON ACT.id = QA.cp_action_id AND ACT.del_flg = 0
INNER JOIN
    cp_action_groups GR ON GR.id = ACT.cp_action_group_id AND GR.del_flg = 0
INNER JOIN
    cps CP ON CP.id = GR.cp_id AND CP.del_flg = 0 AND DATE_FORMAT(CP.start_date, '%Y-%m-%d') <= ?period_date? AND DATE_FORMAT(CP.end_date, '%Y-%m-%d') >= ?period_date?
INNER JOIN
    questionnaires_questions_relations R ON QA.id = R.cp_questionnaire_action_id AND R.del_flg = 0
WHERE
    QA.del_flg = 0
