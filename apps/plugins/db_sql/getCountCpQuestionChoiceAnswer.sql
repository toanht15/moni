SELECT
    count(A.id) AS total
FROM
    question_choice_answers A
INNER JOIN
    question_choice_requirements CR ON CR.question_id = A.question_id AND CR.del_flg = 0 AND CR.multi_answer_flg = ?multi_answer_flg?
WHERE
    A.del_flg = 0
    AND A.created_at BETWEEN ?created_at_start? AND ?created_at_end?