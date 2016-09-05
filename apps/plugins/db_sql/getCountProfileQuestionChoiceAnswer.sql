SELECT
    count(A.id) AS total
FROM
    profile_question_choice_answers A
INNER JOIN
    profile_questionnaires_questions_relations R ON A.questionnaires_questions_relation_id = R.id AND R.del_flg = 0
INNER JOIN
    profile_question_choice_requirements CR ON CR.question_id = R.question_id AND CR.del_flg = 0 AND CR.multi_answer_flg = ?multi_answer_flg?
WHERE
    A.del_flg = 0
    AND A.created_at BETWEEN ?created_at_start? AND ?created_at_end?